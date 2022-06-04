<?php

namespace App\Controller;

use App\Entity\Email;
use App\Entity\Group;
use App\Entity\Messages;
use App\Form\GroupType;
use App\Form\MessagesType;
use App\Repository\GroupRepository;
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

#[Route('/group')]
class GroupController extends AbstractController
{
    #[Route('/', name: 'group_index', methods: ['GET'])]
    public function index(GroupRepository $groupRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        return $this->render('group/index.html.twig', [
            'groups' => $groupRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'group_new', methods: ['GET','POST'])]
    public function new(Request $request): Response
    {
        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($group);
            $entityManager->flush();

            return $this->redirectToRoute('group_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('group/new.html.twig', [
            'group' => $group,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'group_show', methods: ['GET'])]
    public function show(Group $group): Response
    {
        return $this->render('group/show.html.twig', [
            'group' => $group,
        ]);
    }

    #[Route('/{id}/edit', name: 'group_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Group $group): Response
    {
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('group_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('group/edit.html.twig', [
            'group' => $group,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'group_delete', methods: ['POST'])]
    public function delete(Request $request, Group $group): Response
    {
        if ($this->isCsrfTokenValid('delete'.$group->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($group);
            $entityManager->flush();
        }

        return $this->redirectToRoute('group_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * @param Request $request
     * @param Mailer $mailer
     * @param Group $group
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     */
    #[Route('/{id}/mail', name: 'group_mail', methods: ['GET','POST'])]
    public function mail(Request $request, Mailer $mailer, Group $group): Response
    {
        $sender = $this->getUser();
        $users = $group->getMembers();
        $message = new Messages;
        $form = $this->createForm(MessagesType::class, $message);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            foreach($users as $user) {
                $message->setSender($sender);
                $message->setRecipient($user);
                $em = $this->getDoctrine()->getManager();

                $em->persist($message);
                $em->flush();

                $email = $em->getRepository(Email::class)->findOneBy(['code' => 'EMAIL_GROUP']);

                $loader = new ArrayLoader([
                    'email' => $email->getContenu(),
                ]);

                $twig = new Environment($loader);
                $messageMail = $twig->render('email', ['user' => $this->getUser(), 'messageMail' => $message->getMessage(), 'title' => $message->getTitle()]);

                $mailer->send([
                    'recipient_email' => $message->getRecipient()->getEmail(),
                    'subject' => $message->getTitle(),
                    'html_template' => 'email/email_vide.html.twig',
                    'context' => [
                        'message' => $messageMail
                    ]
                ]);
            }
            $this->addFlash('success', 'Successfully sent.');

            return $this->redirectToRoute('group_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render("group/envoi_mail.html.twig", [
            'form' => $form->createView()
        ]);
    }
}
