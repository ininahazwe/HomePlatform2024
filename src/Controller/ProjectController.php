<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\Project;
use App\Entity\User;
use App\Form\ProjectType;
use App\Repository\GroupRepository;
use App\Repository\ProjectRepository;
use App\Service\Mailer;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/project')]
class ProjectController extends AbstractController
{
    #[Route('/', name: 'project_index', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository): Response
    {
        /**@var User $user */
        $user = $this->getUser();
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');

        return $this->render('project/index.html.twig', [
            'projects' => $projectRepository->getProjectByUser($this->getUser()),
            'user' => $user,
        ]);
    }

    #[Route('/admin/all', name: 'projects_for_admin', methods: ['GET'])]
    public function allProjects(ProjectRepository $projectRepository): Response
    {
        return $this->render('project/findAll.html.twig', [
            'projects' => $projectRepository->findBy([], ['id' => 'DESC']),
        ]);
    }

    #[Route('/pr/new', name: 'project_new', methods: ['GET','POST'])]
    public function new(Request $request, Mailer $mailer, ManagerRegistry $doctrine, GroupRepository $groupRepository): Response
    {
        /**@var User $user */
        $user = $this->getUser();

        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project, [
            'user' => $user,
        ]);

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

            $project->addAuteur($user);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($project);
            $entityManager->flush();

            $this->addFlash('success', 'Successfully added');

            if($user->isSuperAdmin()){
                return $this->redirectToRoute('projects_for_admin', [], Response::HTTP_SEE_OTHER);
            } else {
                return $this->redirectToRoute('project_index', [], Response::HTTP_SEE_OTHER);
            }

        }

        return $this->renderForm('project/new.html.twig', [
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

    #[Route('/{slug}', name: 'project_show', methods: ['GET'])]
    public function show(Project $project): Response
    {
        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/edit/{id}', name: 'project_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Project $project, ManagerRegistry $doctrine): Response
    {
        /**@var User $user */
        $user = $this->getUser();

        $form = $this->createForm(ProjectType::class, $project, [
            'user' => $user,
        ]);
        $form->handleRequest($request);
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('avatar')->getData();
            foreach($images as $image){
                $this->saveDoc($project, $image, File::TYPE_AVATAR);
            }
            $images = $form->get('images')->getData();
            foreach($images as $image){
                $this->saveDoc($project, $image, File::TYPE_ILLUSTRATION);
            }

            $project->updateTimestamps();

            $doctrine->getManager()->flush();

            $this->addFlash('success', 'Successful update');

            if($user->isSuperAdmin()){
                return $this->redirectToRoute('projects_for_admin', [], Response::HTTP_SEE_OTHER);
            } else {
                return $this->redirectToRoute('project_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->renderForm('project/edit.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/delete/ajax/{id}', name: 'project_delete_files')]
    public function deleteImage(File $file, Request $request, ManagerRegistry $doctrine): Response {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($file);
        $entityManager->flush();

        return new Response(1);
    }

    #[Route('/delete/ajax/{id}', name: 'project_delete')]
    public function delete(Request $request, Project $project, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($project);
        $entityManager->flush();

        return new Response(1);
    }



    #[Route('/status/{id}/{statut}', name: 'project_statut', methods: ['GET'])]
    public function statutProject($id, $statut, Request $request, Project $project, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $project = $entityManager->getRepository(Project::class)->find($id);
        $project->setStatut($statut);
        $entityManager->persist($project);
        $entityManager->flush();

        if ($statut == 1){
            return new Response($this->generateUrl('project_statut', ['id' => $id, 'statut' => 0]));
        }else{
            return new Response($this->generateUrl('project_statut', ['id' => $id, 'statut' => 1]));
        }
    }

}
