<?php

namespace App\Controller;

use App\Entity\Email;
use App\Entity\Messages;
use App\Form\MessagesType;
use App\Repository\MessagesRepository;
use App\Service\Mailer;
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
        return $this->render('messages/index.html.twig', [
            'messages' => $messagesRepository->findAll(),
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws TransportExceptionInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    #[Route('/send', name: 'send')]
    public function send(Request $request, Mailer $mailer): Response
    {
        $messageMail = new Messages;
        $form = $this->createForm(MessagesType::class, $messageMail);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $messageMail->setSender($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($messageMail);
            $em->flush();

            $email = $em->getRepository(Email::class)->findOneBy(['code' => 'EMAIL_RECEPTION_EMAIl']);

            $loader = new ArrayLoader([
                'email' => $email->getContenu(),
            ]);

            $twig = new Environment($loader);
            $message = $twig->render('email', ['user' => $this->getUser(), 'messageMail' => $messageMail]);

            $this->addFlash('success', 'Successfully sent.');

            $mailer->send([
                'recipient_email' => $messageMail->getRecipient()->getEmail(),
                'subject' => $email->getSujet(),
                'html_template' => 'email/email_vide.html.twig',
                'context' => [
                    'message' => $message
                ]
            ]);

            return $this->redirectToRoute("messages");
        }

        return $this->render("messages/send.html.twig", [
            "form" => $form->createView()
        ]);
    }

    #[Route('/answer', name: 'answer')]
    public function answer(Request $request, Mailer $mailer): Response
    {
        $messageMail = new Messages;
        $form = $this->createForm(MessagesType::class, $messageMail);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $messageMail->setSender($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($messageMail);
            $em->flush();

            $email = $em->getRepository(Email::class)->findOneBy(['code' => 'EMAIL_RECEPTION_EMAIl']);

            $loader = new ArrayLoader([
                'email' => $email->getContenu(),
            ]);

            $twig = new Environment($loader);
            $message = $twig->render('email', ['user' => $this->getUser(), 'messageMail' => $messageMail]);

            $this->addFlash('success', 'Successfully sent.');

            $mailer->send([
                'recipient_email' => $messageMail->getRecipient()->getEmail(),
                'subject' => $email->getSujet(),
                'html_template' => 'email/email_vide.html.twig',
                'context' => [
                    'message' => $message
                ]
            ]);

            return $this->redirectToRoute("messages");
        }

        return $this->render("messages/answer.html.twig", [
            "form" => $form->createView()
        ]);
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
    public function read(Messages $message, Request $request): Response
    {
        $message->setIsRead(true);
        $em = $this->getDoctrine()->getManager();
        $em->persist($message);
        $em->flush();

        return $this->render('messages/read.html.twig', compact("message"));
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Messages $message): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($message);
        $em->flush();

        return $this->redirectToRoute("received");
    }
}