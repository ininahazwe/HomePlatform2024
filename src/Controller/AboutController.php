<?php

namespace App\Controller;

use App\Entity\About;
use App\Entity\File;
use App\Form\AboutType;
use App\Repository\AboutRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/about')]
class AboutController extends AbstractController
{
    #[Route('/', name: 'about_index', methods: ['GET'])]
    public function index(AboutRepository $aboutRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        return $this->render('about/index.html.twig', [
            'abouts' => $aboutRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'about_new', methods: ['GET','POST'])]
    public function new(Request $request): Response
    {
        $about = new About();
        $user = $this->getUser();
        $form = $this->createForm(AboutType::class, $about);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();
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
                $about->addImage($img);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($about);
            $entityManager->flush();

            $this->addFlash('success', 'Successfully added');

            return $this->redirectToRoute('about_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('about/new.html.twig', [
            'about' => $about,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'about_show', methods: ['GET'])]
    public function show(About $about): Response
    {
        return $this->render('about/show.html.twig', [
            'about' => $about,
        ]);
    }

    #[Route('/{id}/edit', name: 'about_edit', methods: ['GET','POST'])]
    public function edit(Request $request, About $about): Response
    {
        $form = $this->createForm(AboutType::class, $about);
        $user = $this->getUser();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();
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
                $about->addImage($img);
            }

            $about->updateTimestamps();

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Successful update');

            return $this->redirectToRoute('about_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('about/edit.html.twig', [
            'about' => $about,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'about_delete', methods: ['POST'])]
    public function delete(Request $request, About $about): Response
    {
        if ($this->isCsrfTokenValid('delete'.$about->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($about);
            $entityManager->flush();
        }

        return $this->redirectToRoute('about_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/supprime/image/{id}', name: 'about_delete_image', methods: ['DELETE'])]
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
