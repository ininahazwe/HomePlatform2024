<?php

namespace App\Controller;

use App\Entity\Messages;
use App\Entity\User;
use App\Repository\CategorieRepository;
use App\Repository\MessagesRepository;
use App\Repository\ProfileRepository;
use App\Repository\ProjectRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    public function index(ProjectRepository $projectRepository, CategorieRepository $categorieRepository, MessagesRepository $messagesRepository, ProfileRepository $profileRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');

        /** @var User $user */

        $user = $this->getUser();
        $profileExiste = $profileRepository->findOneBy(['user' => $user->getId()]);

        $profile = null;
        if ($profileExiste) {
            $profile = $profileExiste;
        }

        $projects = $projectRepository->getProjectPublished();
        $categories = $categorieRepository->findAll();
        return $this->render('dashboard/index.html.twig', [
            'profile' => $profile,
            'projects' => $projects,
            'user' => $user,
            'categories' => $categories,
            'message' => $messagesRepository->findAll()
        ]);
    }

    /**
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    public function messageNotReadAdmin(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $messages = $em->getRepository(Messages::class)->countMsgNotRead($this->getUser());
        return new Response("".$messages);
    }

}
