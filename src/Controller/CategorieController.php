<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\File;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categorie')]
class CategorieController extends AbstractController
{
    #[Route('/', name: 'categorie_index', methods: ['GET'])]
    public function index(CategorieRepository $categorieRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MENTOR');
        return $this->render('categorie/index.html.twig', [
            'categories' => $categorieRepository->findBy([], ['id' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'categorie_new', methods: ['GET','POST'])]
    public function new(Request $request, ManagerRegistry $doctrine): Response
    {
        $categorie = new Categorie();
        $user = $this->getUser();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('logo')->getData();
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
                $img->setType(File::TYPE_LOGO);
                $categorie->addLogo($img);
            }

            $entityManager = $doctrine->getManager();
            $entityManager->persist($categorie);
            $entityManager->flush();

            $this->addFlash('success', 'Successfully added');

            return $this->redirectToRoute('categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('categorie/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'categorie_show', methods: ['GET'])]
    public function show(Categorie $categorie): Response
    {
        return $this->render('categorie/show.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    #[Route('/{id}/edit', name: 'categorie_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Categorie $categorie, ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('logo')->getData();
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
                $img->setType(File::TYPE_LOGO);
                $categorie->addLogo($img);
            }

            $categorie->updateTimestamps();

            $doctrine->getManager()->flush();

            $this->addFlash('success', 'Successful update');

            return $this->redirectToRoute('categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('categorie/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'categorie_delete', methods: ['POST'])]
    public function delete(Request $request, Categorie $categorie, ManagerRegistry $doctrine): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorie->getId(), $request->request->get('_token'))) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($categorie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('categorie_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/delete/ajax/{id}', name: 'categorie_delete_files')]
    public function deleteImage(File $file, Request $request, ManagerRegistry $doctrine): Response {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($file);
        $entityManager->flush();

        return new Response(1);
    }
}
