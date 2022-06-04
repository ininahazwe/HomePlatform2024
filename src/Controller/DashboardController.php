<?php

namespace App\Controller;

use App\Entity\Messages;
use App\Repository\CategorieRepository;
use App\Repository\MessagesRepository;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    public function index(ProjectRepository $projectRepository, CategorieRepository $categorieRepository, MessagesRepository $messagesRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');

        $user = $this->getUser();
        $projects = $projectRepository->getProjectPublished();
        $categories = $categorieRepository->findAll();
        return $this->render('dashboard/index.html.twig', [
            'projects' => $projects,
            'user' => $user,
            'categories' => $categories,
            'message' => $messagesRepository->findAll()
        ]);
    }

    /**
     * @return Response
     */
    public function messageNotReadAdmin(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $messages = $em->getRepository(Messages::class)->countMsgNotRead($this->getUser());
        return new Response("".$messages."");
    }


}
