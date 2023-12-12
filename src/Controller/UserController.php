<?php

namespace App\Controller;

use App\Entity\Desinscription;
use App\Entity\Email;
use App\Entity\User;
use App\Form\CreateUserType;
use App\Form\DesinscriptionType;
use App\Form\RoleType;
use App\Form\UserUpdateType;
use App\Repository\ProfileRepository;
use App\Repository\UserRepository;
use App\Service\Mailer;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\ArrayLoader;

#[Route('/cms/users')]
class UserController extends AbstractController
{
    #[Route('/', name: 'all_users', methods: ['GET'])]
    public function index(UserRepository $userRepository, ProfileRepository $profileRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MENTOR');

        $users = $userRepository->findAll();

        foreach ($users as $user) {
            $profile = $profileRepository->findOneBy(['user' => $user]);
        }

        return $this->render('user/index.html.twig', [
            'users' => $users,
            'prof' => $profile
        ]);
    }

    #[Route('/deleted', name: 'users_delete', methods: ['GET'])]
    public function deleted(UserRepository $userRepository, ProfileRepository $profileRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MENTOR');

        $users = $userRepository->findby(['status' => User::COMPTE_SUPPRIME]);

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/{id}/edit', name: 'user_edit', methods: ['GET','POST'])]
    public function edit(Request $request, User $user, ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(UserUpdateType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine->getManager()->flush();

            $user->updateTimestamps();
            $this->addFlash('success', 'Successfully updated');

            return $this->redirectToRoute('gestion_mon_compte', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/role', name: 'role_edit', methods: ['GET','POST'])]
    public function editRole(Request $request, User $user, ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(RoleType::class, $user, [
                'user' => $this->getUser(),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $roles[] = $form->get('roles')->getData();
            $user->setRoles($roles);

            $user->updateTimestamps();
            $doctrine->getManager()->flush();

            $this->addFlash('success', 'Mise à jour réussie');

            return $this->redirectToRoute('user_edit', ['id' => $user->getId()]);

        }

        return $this->renderForm('user/role.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /*#[Route('/{id}', name: 'user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
    }*/

    #[Route('/{id}/deleteAccount', name: 'user_delete_account', methods: ['GET'])]
    public function deleteAccount($id, Request $request, User $user, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);
        $user->setIsDeleted(true);
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Account deleted successfully');

        return $this->redirectToRoute('all_users', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/create/new-user', name: 'user_create', methods: ['GET', 'POST'])]
    public function createUser(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $userPasswordHasher,
        LoginLinkHandlerInterface $loginLinkHandler,
        MailerInterface $mailer,
        ManagerRegistry $doctrine
    ): Response
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
        $entityManager = $doctrine->getManager();

        if ($form->isSubmitted() && $form->isValid()) {
            $mentor = $request->get('isMentor');
            if (!$userExist) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $password
                    )
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
                ->text('Hello' . '' . $user->getFullname() . '<br>' . 'You can use this link to login: ' . '.' . $loginLinkDetails->getUrl()) . 'br>' . 'Home team';

            $mailer->send($email);

            $this->addFlash('success', 'Ajout réussi');

            return $this->redirectToRoute('user_index',[], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'users' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param $request
     * @param $mailer
     * @param $entityManager
     * @param $options
     * @param $code
     * @param $recipient
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function sendEmail($request, $mailer, $entityManager, $options, $code, $recipient): void
    {
        $email = $entityManager->getRepository(Email::class)->findOneBy(['code' => $code]);

        $loader = new ArrayLoader([
            'email' => $email->getContenu(),
            'sujet' => $email->getSujet()
        ]);

        $twig = new Environment($loader);
        $message = $twig->render('email', $options);
        $sujet = $twig->render('sujet', $options);

        $mailer->send([
            'from' => null,
            'recipient_email' => $recipient,
            'subject' => $sujet,
            'html_template' => 'email/email_vide.html.twig',
            'context' => [
                'url' => "https://{$request->getHost()}",
                'message' => $message
            ]
        ]);
    }
}
