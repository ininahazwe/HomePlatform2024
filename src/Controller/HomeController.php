<?php

namespace App\Controller;

use App\Entity\About;
use App\Repository\AboutRepository;
use App\Repository\CategorieRepository;
use App\Repository\EditionRepository;
use App\Repository\PartnerRepository;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(CategorieRepository $categorieRepository, AboutRepository $aboutRepository, EditionRepository $editionRepository, PartnerRepository $partnerRepository, ProjectRepository $projectRepository): Response
    {
        $partners = $partnerRepository->findAll();
        $projects = $projectRepository->findAll();
        $categories = $categorieRepository->findAll();
        $editions = $editionRepository->findAll();
        $about = $aboutRepository->findAll();
        return $this->render('home/index.html.twig', [
            'categories' => $categories,
            'abouts' => $about,
            'editions' => $editions,
            'partners' => $partners,
            'projects' => $projects,
        ]);
    }

    #[Route('/{slug}', name: 'about_show', methods: ['GET'])]
    public function show(About $about): Response
    {
        return $this->render('about/show.html.twig', [
            'about' => $about,
        ]);
    }
}
