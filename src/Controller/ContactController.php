<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function index(Request $request, Mailer $mailer):Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $contactFormData = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $email = $em->getRepository(Email::class)->findOneBy(['code' => 'EMAIL_CONTACT']);

            $loader = new ArrayLoader([
                'email' => $email->getContenu(),
            ]);

            $twig = new Environment($loader);
            $message = $twig->render('email', ['user' => $this->getUser(), 'messageMail' => $contactFormData]);

            $this->addFlash('success', 'Successfully sent.');

            $mailer->send([
                'recipient_email' => 'yvesininahazwe@gmail.com',
                'subject' => $email->getSujet(),
                'html_template' => 'email/email_vide.html.twig',
                'context' => [
                    'message' => $message
                ]
            ]);
            $this->addFlash('success', 'Message sent');
            return $this->redirectToRoute('contact');
        }
        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
