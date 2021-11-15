<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\EditionRepository;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(CategorieRepository $categorieRepository, EditionRepository $editionRepository, ProjectRepository $projectRepository): Response
    {
        $projects = $projectRepository->findAll();
        $categories = $categorieRepository->findAll();
        $editions = $editionRepository->findAll();
        return $this->render('home/index.html.twig', [
            'categories' => $categories,
            'editions' => $editions,
            'projects' => $projects,
        ]);
    }
}
