<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\File;
use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\CategorieRepository;
use App\Repository\EditionRepository;
use App\Repository\PartenairesRepository;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(CategorieRepository $categorieRepository, EditionRepository $editionRepository, ProjectRepository $projectRepository, PartenairesRepository $partenairesRepository): Response
    {
        $projects = $projectRepository->findAll();
        $partenaires = $partenairesRepository->findAll();
        $categories = $categorieRepository->findLatest();
        $editions = $editionRepository->findAll();
        return $this->render('home/index.html.twig', [
            'categories' => $categories,
            'editions' => $editions,
            'projects' => $projects,
            'partenaires' => $partenaires,
        ]);
    }

    #[Route('/sdg', name: 'all_sdg', methods: ['GET'])]
    public function allSdg(CategorieRepository $categorieRepository): Response
    {
        return $this->render('categorie/allSDG.html.twig', [
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    #[Route('/sdg/{slug}', name: 'sdg_page', methods: ['GET'])]
    public function OneSdg(Categorie $categorie, CategorieRepository $categorieRepository): Response
    {
        return $this->render('categorie/sdg-page.html.twig', [
            'categorie' => $categorie,
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    #[Route('/projects', name: 'all_projects', methods: ['GET'])]
    public function allProjects(ProjectRepository $projectRepository): Response
    {
        return $this->render('project/AllProjects.html.twig', [
            'projects' => $projectRepository->findAll(),
        ]);
    }

    #[Route('/project/{slug}', name: 'project_page', methods: ['GET'])]
    public function OneProject(Project $project, ProjectRepository $projectRepository): Response
    {
        return $this->render('project/project-page.html.twig', [
            'project' => $project,
            'projects' => $projectRepository->findAll(),
        ]);
    }

    #[Route('/projects/new', name: 'project_new_home', methods: ['GET','POST'])]
    public function newProject(Request $request, ProjectRepository $projectRepository): Response
    {
        $project = new Project();
        $user = $this->getUser();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();
            foreach($images as $image){
                $fichier = md5(uniqid()).'.'.$image->guessExtension();
                $name = $image->getClientOriginalName();

                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                $img = new File();
                $img->setNom($fichier);
                $img->setUser($user);
                $img->setNomFichier($name);
                $img->setType(File::TYPE_LOGO);
                $project->addImage($img);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($project);
            $entityManager->flush();

            $this->addFlash('success', 'Ajout réussi');

            return $this->redirectToRoute('project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('home/project_new.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }
}
