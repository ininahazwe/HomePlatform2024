<?php

namespace App\Controller;

use App\Entity\Desinscription;
use App\Entity\Email;
use App\Entity\User;
use App\Form\DesinscriptionType;
use App\Form\UserType;
use App\Repository\ProfileRepository;
use App\Service\Mailer;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\ArrayLoader;

#[Route('/account')]
class AccountController extends AbstractController
{
    #[Route('/my-account', name: 'gestion_mon_compte')]
    public function parametres(ProfileRepository $profileRepository): Response
    {
        /**@var User $user */
        $user = $this->getUser();
        $profile = $profileRepository->findOneBy(['user' => $user]);

        return $this->render('account/index.html.twig',[
                'user' => $user,
                'profile' => $profile,
        ]);
    }

    #[Route('/edit', name: 'account_edit', methods: ['GET','POST'])]
    public function editAccount(Request $request, ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user, [
                        'user' => $this->getUser(),
                ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->updateTimestamps();
            $doctrine->getManager()->flush();

            $this->addFlash('success', 'Mise à jour réussie');

            if ($referer = $request->get('referer', false)) {
                $referer = base64_decode($referer);
                return $this->redirect(($referer));
            } else {
                return $this->redirectToRoute('gestion_mon_compte');
            }
        }

        return $this->renderForm('user/edit.html.twig', [
                'user' => $user,
                'form' => $form,
        ]);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws SyntaxError
     * @throws ContainerExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/delete/{id}', name: 'account_delete')]
    public function delete(Request $request, User $user, $id, ManagerRegistry $doctrine, Mailer $mailer, ProfileRepository $profileRepository): Response
    {
        /** @var User $admin */
        $admin = $this->getUser();

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $email = $user->getEmail();
        $entityManager = $doctrine->getManager();

        $user = $entityManager->getRepository(User::class)->find($id);

        $desinscription = new Desinscription();
        $form = $this->createForm(DesinscriptionType::class, $desinscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$profile = $profileRepository->findOneBy(['user' => $user]);
            //$desinscription->setGenre($user->getCivilite());
            $desinscription->setDateInscription($user->getCreatedAt());
            $desinscription->setPrenom($user->getPrenom());
            $desinscription->setNom($user->getNom());
            $desinscription->setEmail($user->getEmail());
            $entityManager->persist($desinscription);

            $entityManager->remove($user);
            $this->container->get('security.token_storage')->setToken(null);
            $entityManager->flush();

            $this->addFlash("success", "Success");

            $this->sendEmail($request, $mailer, $entityManager, ['email' => $email], 'C_EMAIL_SUPPRESSION_COMPTE', $email);
            $this->sendEmail($request, $mailer, $entityManager, ['email' => $email], 'A_NOTIFICATION_SUPPRESSION_COMPTE', 'app@talents-handicap.com');

            if($admin->isSuperAdmin()){
                if ($referer = $request->get('referer', false)) {
                    $referer = base64_decode($referer);
                    return $this->redirect(($referer));
                } else {
                    return $this->redirectToRoute('user_index');
                }
            } else {
                return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->renderForm('account/desinscription.html.twig', [
            'form' => $form,
            'user' => $user
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

