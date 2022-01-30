<?php

namespace App\Controller;

use App\Data\SearchDataProject;
use App\Entity\Categorie;
use App\Entity\Edition;
use App\Entity\File;
use App\Entity\Project;
use App\Form\ContactType;
use App\Form\ProjectContactType;
use App\Form\ProjectType;
use App\Form\Search\SearchProjectForm;
use App\Repository\AboutRepository;
use App\Repository\CategorieRepository;
use App\Repository\EditionRepository;
use App\Repository\PartenairesRepository;
use App\Repository\ProjectRepository;
use App\Repository\TeamRepository;
use App\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Stopwatch $stopwatch, CacheInterface $cache,
        Request $request, AboutRepository $aboutRepository, CategorieRepository $categorieRepository, EditionRepository $editionRepository, ProjectRepository $projectRepository, PartenairesRepository $partenairesRepository): Response
    {
        $projects = $projectRepository->getProjectPublished();
        $abouts = $aboutRepository->findAll();
        $partenaires = $partenairesRepository->findAll();
        $categories = $categorieRepository->findLatest();
        $editions = $editionRepository->findAll();

        $data = new SearchDataProject();
        $data->page = $request->get('page', 1);
        $form = $this->createForm(SearchProjectForm::class, $data);
        $form->handleRequest($request);

        $stopwatch->start('calcul-long');
        $resultatCalcul = $cache->get('resultat-calcul-long', function (ItemInterface $item){
            $item->expiresAfter(10);
            return $this->fonctionQuiPrendDuTemps();
        });
        $stopwatch->stop('calcul-long');
        return $this->render('home/index.html.twig', [
            'categories' => $categories,
            'abouts' => $abouts,
            'editions' => $editions,
            'projects' => $projects,
            'partenaires' => $partenaires,
            'form' => $form->createView()
        ]);
    }

    private function fonctionQuiPrendDuTemps(): int
    {
        sleep(2);

        return 10;
    }

    #[Route('/sdg', name: 'all_sdg', methods: ['GET'])]
    public function allSdg(CategorieRepository $categorieRepository): Response
    {
        return $this->render('categorie/allSDG.html.twig', [
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    #[Route('/editions', name: 'all_editions', methods: ['GET'])]
    public function allEditions(EditionRepository $editionRepository): Response
    {
        return $this->render('edition/allEditions.html.twig', [
            'categories' => $editionRepository->findAll(),
        ]);
    }

    #[Route('/editions/{slug}', name: 'edition_page', methods: ['GET'])]
    public function OneEdition(Edition $edition): Response
    {
        return $this->render('edition/page.html.twig', [
            'edition' => $edition
        ]);
    }

    #[Route('/about-us', name: 'about-page', methods: ['GET'])]
    public function about(TeamRepository $teamRepository, AboutRepository $aboutRepository): Response
    {
        $teams = $teamRepository->findByOrder();

        return $this->render('home/about_page.html.twig', [
            'about' => $aboutRepository->findOneBy(['id' => 1]),
            'teams' => $teams,
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
    public function allProjects(Request $request, ProjectRepository $projectRepository): Response
    {
        $data = new SearchDataProject();
        $data->page = $request->get('page', 1);
        $form = $this->createForm(SearchProjectForm::class, $data);
        $form->handleRequest($request);

        $projects = $projectRepository->findSearch($data);

        return $this->render('project/AllProjects.html.twig', [
            'projects' => $projects,
            'form' => $form->createView()
        ]);
    }

    #[Route('/project/{slug}', name: 'project_page', methods: ['GET'])]
    public function OneProject(Request $request, Project $project, $slug, ProjectRepository $projectRepository, CacheInterface $cache, Mailer $mailer): Response
    {
        $project = $cache->get('project'.$slug, function(ItemInterface $item) use($projectRepository, $slug){
            $item->expiresAfter(20);
            return $projectRepository->findOneBy(['slug' => $slug]);
        });

        $form = $this->createForm(ProjectContactType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $contactFormData = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $email = $em->getRepository(Email::class)->findOneBy(['code' => 'EMAIL_PROJECT_CONTACT']);

            $loader = new ArrayLoader([
                'email' => $email->getContenu(),
            ]);

            $twig = new Environment($loader);
            $message = $twig->render('email', ['user' => $this->getUser(), 'messageMail' => $contactFormData]);

            $this->addFlash('success', 'Successfully sent.');

            $mailer->send([
                'recipient_email' => 'yvesininahazwe@gmail.com',
                'subject' => $email->getSujet(),
                'html_template' => 'email/email_vide.html.twig',
                'context' => [
                    'message' => $message
                ]
            ]);
            $this->addFlash('success', 'Message sent');
            return $this->redirectToRoute('contact');
        }

        $projects = $projectRepository->getProjectPublished();

        return $this->render('project/project-page.html.twig', [
            'project' => $project,
            'projects' => $projects,
        ]);
    }

    #[Route('/project/new', name: 'project_new_home', methods: ['GET','POST'])]
    public function newProject(Request $request, ProjectRepository $projectRepository): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('avatar')->getData();
            foreach($images as $image){
                $this->saveDoc($project, $image, File::TYPE_AVATAR);
            }
            $images = $form->get('images')->getData();
            foreach($images as $image){
                $this->saveDoc($project, $image, File::TYPE_ILLUSTRATION);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($project);
            $entityManager->flush();

            $this->addFlash('success', 'Successfully added');

            return $this->redirectToRoute('project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('home/project_new.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    public function saveDoc($project, $image, $type)
    {
        $user = $this->getUser();
        $fichier = md5(uniqid()) . '.' . $image->guessExtension();
        $nomFichier = $image->getClientOriginalName();
        $image->move($this->getParameter('files_directory'), $fichier);
        $img = new File();

        $img->setNom($fichier);
        $img->setUser($user);
        $img->setNomFichier($nomFichier);
        if ($type == File::TYPE_AVATAR){
            $img->setProjectAvatar($project);
            $img->setType($type);
            $project->addAvatar($img);
        }
        if ($type == File::TYPE_ILLUSTRATION){
            $img->setProject($project);
            $img->setType($type);
            $project->addImage($img);
        }
    }

    #[Route('/legal-mentions', name: 'mentions')]
    public function mentions(): Response
    {
        return $this->renderForm('home/mentions.html.twig', [
        ]);
    }

    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $contactFormData = $form->getData();

            $message = (new Email())
                ->from($contactFormData['email'])
                ->to('home.internationalprojects@gmail.com')
                ->subject('New message from contact from')
                ->text('De : '.$contactFormData['nom'].\PHP_EOL.
                    'Email : '.$contactFormData['email'].\PHP_EOL.
                    'Subject : '.$contactFormData['subject'].\PHP_EOL.
                    'Message : '.$contactFormData['message'],
                    'text/plain');

            $mailer->send($message);

            $this->addFlash('success', 'Message sent');

            return $this->redirectToRoute('contact', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }


}
