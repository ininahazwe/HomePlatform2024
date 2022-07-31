<?php

namespace App\Controller;

use App\Entity\Email;
use App\Entity\Messages;
use App\Form\MessagesType;
use App\Repository\MessagesRepository;
use App\Service\Mailer;
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

#[Route('/messages')]
class MessagesController extends AbstractController
{
    #[Route('/', name: 'messages')]
    public function index(MessagesRepository $messagesRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('messages/index.html.twig', [
            'received' => $messagesRepository->getReceived($this->getUser())
        ]);
    }


    /**
     * @param Request $request
     * @param Mailer $mailer
     * @param ManagerRegistry $doctrine
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     */
    #[Route('/send', name: 'send')]
    public function send(Request $request, Mailer $mailer, ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();
        $message = new Messages;
        $form = $this->createForm(MessagesType::class, $message, [
            'user' => $this->getUser(),
        ]);

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

            $email = $em->getRepository(Email::class)->findOneBy(['code' => 'EMAIL_RECEPTION_MESSAGE_INTERNE']);

            $loader = new ArrayLoader([
                'email' => $email->getContenu(),
            ]);

            $twig = new Environment($loader);
            $messageMail = $twig->render('email', ['user' => $user, 'messageMail' => $message->getMessage(), 'sender' => $user, 'recipient' => $message->getRecipient()]);

            $this->addFlash('success', 'Successfully sent.');

            $mailer->send([
                'recipient_email' => $message->getRecipient()->getEmail(),
                'subject' => $email->getSujet(),
                'html_template' => 'email/email_vide.html.twig',
                'context' => [
                    'message' => $messageMail
                ]
            ]);

            return $this->redirectToRoute("messages");
        }

        return $this->render("messages/send.html.twig", [
            "form" => $form->createView()
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/reply/{id}', name: 'reply')]
    public function reply(Request $request, Mailer $mailer, Messages $message, ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();
        $reply = new Messages;
        $em = $doctrine->getManager();
        $textReply = $request->request->get('reply');

        $reply->setSender($user);
        $reply->setRecipient($message->getSender());
        $reply->setMessage($textReply);
        $reply->setTitle('Re: ' . $message->getTitle());
        $reply->setParent($message);

        $em->persist($reply);
        $em->flush();

        $email = $em->getRepository(Email::class)->findOneBy(['code' => 'EMAIL_RECEPTION_MESSAGE_INTERNE']);

        $loader = new ArrayLoader([
            'email' => $email->getContenu(),
        ]);

        $twig = new Environment($loader);
        $messageMail = $twig->render('email', ['user' => $user, 'messageMail' => $message->getMessage(), 'recipient' => $message->getRecipient()]);

        $this->addFlash('success', 'Successfully sent.');

        $mailer->send([
            'recipient_email' => $message->getRecipient()->getEmail(),
            'subject' => $email->getSujet(),
            'html_template' => 'email/email_vide.html.twig',
            'context' => [
                'message' => $messageMail
            ]
        ]);

        return $this->redirectToRoute('read',['id' => $message->getId()]);

    }

    #[Route('/received', name: 'received')]
    public function received(): Response
    {
        return $this->render('messages/received.html.twig');
    }

    #[Route('/sent', name: 'sent')]
    public function sent(): Response
    {
        return $this->render('messages/sent.html.twig');
    }

    #[Route('/read/{id}', name: 'read')]
    public function read(Messages $message, Request $request, ManagerRegistry $doctrine): Response
    {
        $message->setIsRead(true);
        $em = $doctrine->getManager();
        $em->persist($message);
        $em->flush();

        $responses = $em->getRepository(Messages::class)->getResponses($message);
        $received = $em->getRepository(Messages::class)->getReceived($this->getUser());

        return $this->render('messages/read.html.twig', [
            'message' => $message,
            'received' => $received,
            'responses' => $responses,
        ]);

    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Messages $message, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $em->remove($message);
        $em->flush();

        return $this->redirectToRoute("received");
    }

}
