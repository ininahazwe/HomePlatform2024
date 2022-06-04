<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\Team;
use App\Form\TeamType;
use App\Repository\TeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/team')]
class TeamController extends AbstractController
{
    #[Route('/', name: 'team_index', methods: ['GET'])]
    public function index(TeamRepository $teamRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MENTOR');

        return $this->render('team/index.html.twig', [
            'teams' => $teamRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'team_new', methods: ['GET','POST'])]
    public function new(Request $request): Response
    {
        $team = new Team();
        $user = $this->getUser();
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $images = $form->get('photo')->getData();
            foreach($images as $image){
                $fichier = md5(uniqid()).'.'.$image->guessExtension();
                $name = $image->getClientOriginalName();

                $image->move(
                    $this->getParameter('files_directory'),
                    $fichier
                );
                $img = new File();
                $img->setNom($fichier);
                $img->setUser($user);
                $img->setNomFichier($name);
                $img->setType(File::TYPE_ILLUSTRATION);
                $team->addPhoto($img);
            }

            $team->updateTimestamps();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($team);
            $entityManager->flush();

            $this->addFlash('success', 'Successfully added');

            return $this->redirectToRoute('team_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('team/new.html.twig', [
            'team' => $team,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'team_show', methods: ['GET'])]
    public function show(Team $team): Response
    {
        return $this->render('team/show.html.twig', [
            'team' => $team,
        ]);
    }

    #[Route('/{id}/edit', name: 'team_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Team $team): Response
    {
        $form = $this->createForm(TeamType::class, $team);
        $user = $this->getUser();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('photo')->getData();
            foreach($images as $image){
                $fichier = md5(uniqid()).'.'.$image->guessExtension();
                $name = $image->getClientOriginalName();

                $image->move(
                    $this->getParameter('files_directory'),
                    $fichier
                );
                $img = new File();
                $img->setNom($fichier);
                $img->setUser($user);
                $img->setNomFichier($name);
                $img->setType(File::TYPE_ILLUSTRATION);
                $team->addPhoto($img);
            }
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Successful update');

            return $this->redirectToRoute('team_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('team/edit.html.twig', [
            'team' => $team,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'team_delete', methods: ['POST'])]
    public function delete(Request $request, Team $team): Response
    {
        if ($this->isCsrfTokenValid('delete'.$team->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($team);
            $entityManager->flush();
        }

        return $this->redirectToRoute('team_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/supprime/image/{id}', name: 'team_delete_image', methods: ['DELETE'])]
    public function deleteImage(File $image, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if ($this->isCsrfTokenValid('delete' . $image->getId(), $data['_token'])) {
            $nom = $image->getNom();
            unlink($this->getParameter('files_directory') . '/' . $nom);

            $em = $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();

            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }
}
