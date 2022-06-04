<?php

namespace App\Controller;

use App\Entity\Edition;
use App\Entity\File;
use App\Form\EditionType;
use App\Repository\EditionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/edition')]
class EditionController extends AbstractController
{
    #[Route('/', name: 'edition_index', methods: ['GET'])]
    public function index(EditionRepository $editionRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MENTOR');

        return $this->render('edition/index.html.twig', [
            'editions' => $editionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'edition_new', methods: ['GET','POST'])]
    public function new(Request $request): Response
    {
        $edition = new Edition();
        $form = $this->createForm(EditionType::class, $edition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $images = $form->get('avatar')->getData();
            foreach($images as $image){
                $this->saveDoc($edition, $image, File::TYPE_LOGO);
            }
            $images = $form->get('illustration')->getData();
            foreach($images as $image){
                $this->saveDoc($edition, $image, File::TYPE_ILLUSTRATION);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($edition);
            $entityManager->flush();

            $this->addFlash('success', 'Successfully added');

            return $this->redirectToRoute('edition_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('edition/new.html.twig', [
            'edition' => $edition,
            'form' => $form,
        ]);
    }

    public function saveDoc($edition, $image, $type)
    {
        $fichier = md5(uniqid()) . '.' . $image->guessExtension();
        $nomFichier = $image->getClientOriginalName();
        $image->move($this->getParameter('files_directory'), $fichier);
        $img = new File();

        $img->setNom($fichier);
        $img->setNomFichier($nomFichier);
        if ($type == File::TYPE_LOGO){
            $img->setEditionAvatar($edition);
            $img->setType($type);
            $edition->addAvatar($img);
        }
        if ($type == File::TYPE_ILLUSTRATION){
            $img->setEditionIllustration($edition);
            $img->setType($type);
            $edition->addIllustration($img);
        }

    }


    #[Route('/{slug}', name: 'edition_show', methods: ['GET'])]
    public function show(Edition $edition): Response
    {
        return $this->render('edition/show.html.twig', [
            'edition' => $edition,
        ]);
    }

    #[Route('/{id}/edit', name: 'edition_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Edition $edition): Response
    {
        $form = $this->createForm(EditionType::class, $edition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $images = $form->get('avatar')->getData();
            foreach($images as $image){
                $this->saveDoc($edition, $image, File::TYPE_LOGO);
            }
            $images = $form->get('illustration')->getData();
            foreach($images as $image){
                $this->saveDoc($edition, $image, File::TYPE_ILLUSTRATION);
            }

            $edition->updateTimestamps();
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Successful update');

            return $this->redirectToRoute('edition_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('edition/edit.html.twig', [
            'edition' => $edition,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'edition_delete', methods: ['POST'])]
    public function delete(Request $request, Edition $edition): Response
    {
        if ($this->isCsrfTokenValid('delete'.$edition->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($edition);
            $entityManager->flush();
        }

        return $this->redirectToRoute('edition_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/supprime/file/{id}', name: 'editions_delete_files', methods: ['DELETE'])]
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
