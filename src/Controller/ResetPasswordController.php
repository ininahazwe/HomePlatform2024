<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Form\ResetPasswordUserType;
use App\Security\AppCustomAuthenticator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

#[Route('/reset-password')]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    private ResetPasswordHelperInterface $resetPasswordHelper;

    public function __construct(ResetPasswordHelperInterface $resetPasswordHelper)
    {
        $this->resetPasswordHelper = $resetPasswordHelper;
    }

    /**
     * Display & process form to request a password reset.
     */
    #[Route('', name: 'app_forgot_password_request')]
    public function request(Request $request, MailerInterface $mailer, ManagerRegistry $registry): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            return $this->processSendingPasswordResetEmail(
                    $form->get('email')->getData(),
                    $mailer,
                    $registry
            );
        }
        if($user)
        {
            return $this->render('reset_password/request_dashboard.html.twig', [
                    'requestForm' => $form->createView(),
            ]);
        }else{
            return $this->render('reset_password/request.html.twig', [
                    'requestForm' => $form->createView(),
            ]);
        }
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/user/send', name: 'app_password_user')]
    public function userRequest(Request $request,UserPasswordHasherInterface $userPasswordHasherInterface, ManagerRegistry $registry, MailerInterface $mailer): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ResetPasswordUserType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $oldPassword = $request->request->get('reset_password_user')['oldPassword'];

            if ($userPasswordHasherInterface->isPasswordValid($user, $oldPassword)) {

                $encodedPassword = $userPasswordHasherInterface->hashPassword(
                        $user,
                        $form->get('password')->getData()
                );

                $user->setPassword($encodedPassword);

                $registry->getManager()->persist($user);
                $registry->getManager()->flush();

                $this->addFlash('success', 'Réinitialisation du mot de passe réussie');

                return $this->redirectToRoute('gestion_mon_compte');
            } else {
                $this->addFlash('error','Ancien mot de passe incorrect');
            }

            $email = (new TemplatedEmail())
                    ->from(new Address('mailer@home-education.fr', 'Home Mail Bot'))
                    ->to($user->getEmail())
                    ->subject('Your password reset request')
                    ->htmlTemplate('reset_password/email.html.twig')
                    ->context([
                            //'resetToken' => $resetToken,
                    ])
            ;

            $mailer->send($email);

        }
        return $this->render('reset_password/request_dashboard.html.twig', [
                'requestForm' => $form->createView(),
        ]);

    }

    /**
     * Confirmation page after a user has requested a password reset.
     */
    #[Route('/check-email', name: 'app_check_email')]
    public function checkEmail(): Response
    {
        // Generate a fake token if the user does not exist or someone hit this page directly.
        // This prevents exposing whether or not a user was found with the given email address or not
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }

        return $this->render('reset_password/check_email.html.twig', [
            'resetToken' => $resetToken,
        ]);
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     */
    #[Route('/reset/{token}', name: 'app_reset_password')]
    public function reset(
            Request $request,
            UserPasswordHasherInterface $userPasswordHasherInterface,
            string $token = null,
            ManagerRegistry $doctrine,
            UserAuthenticatorInterface $userAuthenticator,
            AppCustomAuthenticator $authenticator,
    ): Response
    {
        if ($token) {
            // We store the token in session and remove it from the URL, to avoid the URL being
            // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('app_reset_password');
        }

        $token = $this->getTokenFromSession();
        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('reset_password_error', sprintf(
                'There was a problem validating your reset request - %s',
                $e->getReason()
            ));

            return $this->redirectToRoute('app_forgot_password_request');
        }

        // The token is valid; allow the user to change their password.
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // A password reset token should be used only once, remove it.
            $this->resetPasswordHelper->removeResetRequest($token);

            // Encode(hash) the plain password, and set it.
            $encodedPassword = $userPasswordHasherInterface->hashPassword(
                $user,
                $form->get('password')->getData()
            );

            $user->setPassword($encodedPassword);
            $doctrine->getManager()->flush();

            // The session is cleaned up after the password has been changed.
            $this->cleanSessionAfterReset();

            $this->addFlash('success', 'New password saved');

            return $userAuthenticator->authenticateUser(
                    $user,
                    $authenticator,
                    $request
            );
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    private function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer, ManagerRegistry $doctrine): RedirectResponse
    {
        $user = $doctrine->getRepository(User::class)->findOneBy([
                'email' => $emailFormData,
        ]);

        // Do not reveal whether a user account was found or not.
        if (!$user) {
            return $this->redirectToRoute('app_check_email');
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            // If you want to tell the user why a reset email was not sent, uncomment
            // the lines below and change the redirect to 'app_forgot_password_request'.
            // Caution: This may reveal if a user is registered or not.
            //
            // $this->addFlash('reset_password_error', sprintf(
            //     'There was a problem handling your password reset request - %s',
            //     $e->getReason()
            // ));

            return $this->redirectToRoute('app_check_email');
        }

        $email = (new TemplatedEmail())
            ->from(new Address('mailer@home-education.fr', 'Home Mail Bot'))
            ->to($user->getEmail())
            ->subject('Your password reset request')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ])
        ;

        $mailer->send($email);

        // Store the token object in session for retrieval in check-email route.
        $this->setTokenObjectInSession($resetToken);

        return $this->redirectToRoute('app_check_email');
    }
}
