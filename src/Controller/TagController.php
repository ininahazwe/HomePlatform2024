<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagType;
use App\Repository\ProjectRepository;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tag')]
class TagController extends AbstractController
{
    #[Route('/', name: 'tag_index', methods: ['GET'])]
    public function index(TagRepository $tagRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MENTOR');

        return $this->render('tag/index.html.twig', [
            'tags' => $tagRepository->findAll(),
        ]);
    }

    #[Route('/{slug}/project', name: 'tag_project', methods: ['GET'])]
    public function project(TagRepository $tagRepository, ProjectRepository $projectRepository): Response
    {
        return $this->render('tag/projects.html.twig', [
            'tags' => $tagRepository->findAll(),
            'projects' => $projectRepository->findAll()
        ]);
    }

    #[Route('/new/ajax/{label}', name: 'tag_new_ajax', methods: ['POST'])]
    public function ajout(Request $request, $label): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $tag = new Tag();
        $tag->setNom(trim(strip_tags($label)));
        $entityManager->persist($tag);
        $entityManager->flush();

        $id = $tag->getId();

        return new JsonResponse(['id' => $id]);
    }



    #[Route('/{id}', name: 'tag_show', methods: ['GET'])]
    public function show(Tag $tag): Response
    {
        return $this->render('tag/show.html.twig', [
            'tag' => $tag,
        ]);
    }

    #[Route('/{id}/edit', name: 'tag_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Tag $tag): Response
    {
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tag->updateTimestamps();
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('tag_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tag/edit.html.twig', [
            'tag' => $tag,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'tag_delete', methods: ['POST'])]
    public function delete(Request $request, Tag $tag): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tag->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($tag);
            $entityManager->flush();
        }

        return $this->redirectToRoute('tag_index', [], Response::HTTP_SEE_OTHER);
    }
}
