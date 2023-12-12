<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\Partenaires;
use App\Form\PartenairesType;
use App\Repository\PartenairesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cms/partenaires')]
class PartenairesController extends AbstractController
{
    #[Route('/', name: 'partenaires_index', methods: ['GET'])]
    public function index(PartenairesRepository $partenairesRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        return $this->render('partenaires/index.html.twig', [
            'partenaires' => $partenairesRepository->findBy([], ['id' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'partenaires_new', methods: ['GET','POST'])]
    public function new(Request $request): Response
    {
        $partenaire = new Partenaires();
        $form = $this->createForm(PartenairesType::class, $partenaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $images = $form->get('logo')->getData();
            foreach($images as $image){
                $this->saveDoc($partenaire, $image, File::TYPE_LOGO);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($partenaire);
            $entityManager->flush();

            $this->addFlash('success', 'Successfully added');

            return $this->redirectToRoute('partenaires_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('partenaires/new.html.twig', [
            'partenaire' => $partenaire,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'partenaires_show', methods: ['GET'])]
    public function show(Partenaires $partenaire): Response
    {
        return $this->render('partenaires/show.html.twig', [
            'partenaire' => $partenaire,
        ]);
    }

    #[Route('/{id}/edit', name: 'partenaires_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Partenaires $partenaire): Response
    {
        $form = $this->createForm(PartenairesType::class, $partenaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $images = $form->get('logo')->getData();
            foreach($images as $image){
                $this->saveDoc($partenaire, $image, File::TYPE_LOGO);
            }

            $partenaire->updateTimestamps();

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Successful update');

            return $this->redirectToRoute('partenaires_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('partenaires/edit.html.twig', [
            'partenaire' => $partenaire,
            'form' => $form,
        ]);
    }

    public function saveDoc($partner, $image, $type)
    {
        $user = $this->getUser();
        $fichier = md5(uniqid()) . '.' . $image->guessExtension();
        $nomFichier = $image->getClientOriginalName();
        $image->move($this->getParameter('files_directory'), $fichier);
        $img = new File();

        $img->setNom($fichier);
        $img->setUser($user);
        $img->setNomFichier($nomFichier);
        $img->setPartenaires($partner);
        $img->setType($type);
        $partner->addLogo($img);
    }

    #[Route('/{id}', name: 'partenaire_delete', methods: ['POST'])]
    public function delete(Request $request, Partenaires $partenaire): Response
    {
        if ($this->isCsrfTokenValid('delete'.$partenaire->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($partenaire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('partenaires_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/supprime/image/{id}', name: 'partenaires_delete_image', methods: ['DELETE'])]
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
