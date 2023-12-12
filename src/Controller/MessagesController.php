<?php

namespace App\Controller;

use App\Entity\Email;
use App\Entity\Messages;
use App\Entity\User;
use App\Form\MessagesType;
use App\Repository\MessagesRepository;
use App\Repository\UserRepository;
use App\Service\Mailer;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\ArrayLoader;

#[Route('/message')]
class MessagesController extends AbstractController
{
    #[Route('/', name: 'messages')]
    public function index(MessagesRepository $messagesRepository, UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $users = $userRepository->findBy(['isDeleted' => 0]);

        $groupmates = $userRepository->getGroupMembers($this->getUser());

        return $this->render('message/index.html.twig', [
            'controller_name' => 'MessagesController',
            'messageRepository' => $messagesRepository,
            'received' => $messagesRepository->getReceivedUsers($this->getUser()),
            'groupmates' => $groupmates,
            'users' => $users
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/send', name: 'message_send')]
    public function send(MessagesRepository $messagesRepository, Request $request, Mailer $mailer, ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();
        $message = new Messages;
        $form = $this->createForm(MessagesType::class, $message, [
                        'user' => $this->getUser(),
                ]
        );

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $message->setSender($user);

            $parentid = $form->get("parentid")->getData();

            $em = $doctrine->getManager();
            if($parentid != null){
                $parent = $em->getRepository(Messages::class)->find($parentid);
            }

            $message->setParent($parent ?? null);
            $em->persist($message);
            $em->flush();

            $email = $em->getRepository(Email::class)->findOneBy(['code' => 'EMAIL_ENVOI_MESSAGE']);

            $loader = new ArrayLoader([
                    'email' => $email->getContenu(),
            ]);

            $twig = new Environment($loader);
            $messageMail = $twig->render('email', ['message' => $message->getMessage(), 'sender' => $user, 'recipient' => $message->getRecipient()]);

            $this->addFlash("success", "Message envoyé avec succès.");

            $mailer->send([
                    'recipient_email' => $message->getRecipient()->getEmail(),
                    'subject' => $message->getTitle(),
                    'html_template' => 'email/email_vide.html.twig',
                    'context' => [
                            'message' => $messageMail
                    ]
            ]);

            return $this->redirectToRoute("messages");
        }

        return $this->render("message/modal.html.twig", [
                "form" => $form->createView(),
                'received' => $messagesRepository->getReceived($this->getUser())
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/reply/{id}', name: 'reply')]
    public function reply(Request $request, Mailer $mailer, User $user, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $moi= $this->getUser();
        $reply = new Messages;

        $textReply = $request->request->get('reply');
        $ajax = $request->request->get('ajax');

        $reply->setSender($moi);
        $reply->setRecipient($user);
        $reply->setMessage($textReply);
        $reply->setTitle('Re: ');
        if ($ajax) {
            $reply->setIsRead(1);
        }else{
            $reply->setIsRead(0);
        }
        //$reply->setParent($message);

            $em->persist($reply);
            $em->flush();

            $email = $em->getRepository(Email::class)->findOneBy(['code' => 'EMAIL_ENVOI_MESSAGE']);
            if ($email) {
                $loader = new ArrayLoader([
                    'email' => $email->getContenu(),
                ]);

                $twig = new Environment($loader);
                $messageMail = $twig->render('email', ['message' => $reply->getMessage(), 'sender' => $moi, 'recipient' => $reply->getRecipient()]);

                $this->addFlash("success", "Message envoyé avec succès.");

                $mailer->send([
                    'recipient_email' => $reply->getRecipient()->getEmail(),
                    'subject' => 'Nouveau message sur Talents Handicap',
                    'html_template' => 'email/email_vide.html.twig',
                    'context' => [
                        'message' => $messageMail
                    ]
                ]);
            }
            if ($ajax){
                return new Response(1);
            }else{
                return $this->redirectToRoute('read',['id' => $user->getId()]);
            }


    }

    #[Route('/read/{id}', name: 'read')]
    public function read(User $user, MessagesRepository $messagesRepository, ManagerRegistry $doctrine, UserRepository $userRepository): Response
    {
        $groupmates = $userRepository->getGroupMembers($this->getUser());
        $em = $doctrine->getManager();
        $messagesReceived = $em->getRepository(Messages::class)->getAllMessagesReceivedNotRead($this->getUser(), $user);

        $users = $userRepository->findBy(['isDeleted' => 0 ]);

        foreach ($messagesReceived as $msg){
            $msg->setIsRead(1);
            $em->persist($msg);
        }
        $em->flush();

        $messages = $em->getRepository(Messages::class)->getAllMessages($this->getUser(), $user);

        return $this->render('message/read.html.twig', [
                'user' => $user,
                'message' => $messages,
                'received' => $messagesRepository->getReceivedUsers($this->getUser()),
                'groupmates' => $groupmates,
                'users' => $users
        ]);
    }

    public function messageNotReadBySender(User $user, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $messages = $em->getRepository(Messages::class)->countMsgNotRead($this->getUser(), $user);
        return new Response("".$messages);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function lastMessageReceived(User $user, MessagesRepository $messagesRepository): Response
    {
        $message = $messagesRepository->getLastMessage($this->getUser(), $user);

        return new Response("".$message);
    }

    #[Route('/all/message/{id}', name: 'ajax_all_messages')]
    public function allMessages(User $user, MessagesRepository $messagesRepository): Response
    {
        $messages = $messagesRepository->getAllMessages($this->getUser(), $user);
        return $this->render('message/_messages.html.twig', [
            'user' => $user,
            'messages' => $messages,
        ]);
    }
}
