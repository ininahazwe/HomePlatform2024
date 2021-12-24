<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use App\Service\Mailer;
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
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');

        return $this->render('project/index.html.twig', [
            'projects' => $projectRepository->getProjectByUser($this->getUser()),
        ]);
    }

    #[Route('/admin/all', name: 'projects_for_admin', methods: ['GET'])]
    public function allProjects(ProjectRepository $projectRepository): Response
    {
        return $this->render('project/findAll.html.twig', [
            'projects' => $projectRepository->findAll(),
        ]);
    }

    #[Route('/pr/new', name: 'project_new', methods: ['GET','POST'])]
    public function new(Request $request, Mailer $mailer): Response
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
    public function edit(Request $request, Project $project): Response
    {
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

            $project->updateTimestamps();

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Successful update');

            return $this->redirectToRoute('project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('project/edit.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'project_delete', methods: ['POST'])]
    public function delete(Request $request, Project $project): Response
    {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($project);
            $entityManager->flush();
        }

        return $this->redirectToRoute('project_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/supprime/file/{id}', name: 'projects_delete_files', methods: ['DELETE'])]
    public function deleteImage(File $file, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if($this->isCsrfTokenValid('delete'.$file->getId(), $data['_token'])){
            $nom = $file->getNom();
            unlink($this->getParameter('files_directory').'/'.$nom);

            $em = $this->getDoctrine()->getManager();
            $em->remove($file);
            $em->flush();

            return new JsonResponse(['success' => 1]);
        }else{
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }
}
