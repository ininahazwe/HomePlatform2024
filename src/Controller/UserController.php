<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\CreateUserType;
use App\Form\UserUpdateType;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MENTOR');

        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/{id}/edit', name: 'user_edit', methods: ['GET','POST'])]
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserUpdateType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $user->updateTimestamps();
            $this->addFlash('success', 'Successfully updated');

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/deleteAccount', name: 'user_delete_account', methods: ['GET'])]
    public function deleteAccount($id, Request $request, User $user): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);
        $user->setIsDeleted(true);
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Account deleted successfully');

        return $this->redirectToRoute('logout', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/create/new-user', name: 'user_create', methods: ['GET', 'POST'])]
    public function createUser(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher, LoginLinkHandlerInterface $loginLinkHandler, MailerInterface $mailer): Response
    {
        $userExist = $userRepository->findOneBy(['email' =>$request->get('user[email]')]);
        $password = $userRepository->genererMDP();

        if ($userExist){
            $user = $userExist;
        }else{
            $user = new User();
        }

        $form = $this->createForm(CreateUserType::class, $user);
        $form->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();

        if ($form->isSubmitted() && $form->isValid()) {
            $mentor = $request->get('isMentor');
            if (!$userExist) {
                $user->setPassword('bubanza'
                    /*$userPasswordHasher->hashPassword(
                        $user,
                        $password
                    )*/
                );
            }
            if ($mentor){
                $user->setRoles(['ROLE_MENTOR']);
            }else{
                $user->setRoles(['ROLE_CANDIDAT']);
            }

            $user->setIsVerified(1);
            $entityManager->persist($user);
            $entityManager->flush();

            $loginLinkDetails = $loginLinkHandler->createLoginLink($user);
            $email = (new TemplatedEmail())
                ->from(new Address('contact@home-education.fr', 'Home Mail Bot'))
                ->to($user->getEmail())
                ->subject('Welcome to Home Platform')
                ->text('Hello' . '' . $user->getFullname() . '' . 'You can use this link to login: ' . '.' . $loginLinkDetails->getUrl());

            $mailer->send($email);

            $this->addFlash('success', 'Ajout réussi');

            return $this->redirectToRoute('user_index',[], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'users' => $user,
            'form' => $form->createView(),
        ]);
    }
}
