<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\Profile;
use App\Form\ProfileType;
use App\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profile')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'profile_index', methods: ['GET'])]
    public function index(ProfileRepository $profileRepository): Response
    {
        return $this->render('profile/index.html.twig', [
            'profiles' => $profileRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'profile_new', methods: ['GET','POST'])]
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');

        $profile = new Profile();
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('photo')->getData();
            foreach ($images as $image) {
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();
                $nomFichier = $image->getClientOriginalName();
                $image->move($this->getParameter('files_directory'), $fichier);
                $img = new File();
                $img->setUser($user);
                $img->setNom($fichier);
                $img->setNomFichier($nomFichier);
                $img->setType(File::TYPE_AVATAR);
                $profile->addPhoto($img);
                $profile->setUser($user);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($profile);
            $entityManager->flush();

            $this->addFlash('success', 'Successfully added');

            return $this->redirectToRoute('profile_show', ['id' => $profile->getId()], Response::HTTP_SEE_OTHER);
        }


        return $this->renderForm('profile/new.html.twig', [
            'profile' => $profile,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'profile_show', methods: ['GET'])]
    public function show(Profile $profile): Response
    {
        $user = $this->getUser();
        return $this->render('profile/show.html.twig', [
            'profile' => $profile,
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'profile_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Profile $profile): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $profile);
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
                $img->setProfile($profile);
                $img->setNomFichier($name);
                $img->setType(File::TYPE_LOGO);
                $profile->addPhoto($img);
            }
            $profile->updateTimestamps();

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Successful update');

            return $this->redirectToRoute('profile_show', ['id' => $profile->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('profile/edit.html.twig', [
            'profile' => $profile,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'profile_delete', methods: ['POST'])]
    public function delete(Request $request, Profile $profile): Response
    {
        if ($this->isCsrfTokenValid('delete'.$profile->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($profile);
            $entityManager->flush();
        }

        return $this->redirectToRoute('profile_index', [], Response::HTTP_SEE_OTHER);
    }

    public function saveDoc($profile, $image, $type)
    {
        $fichier = md5(uniqid()) . '.' . $image->guessExtension();
        $nomFichier = $image->getClientOriginalName();
        $image->move($this->getParameter('files_directory'), $fichier);
        $img = new File();

        $img->setNom($fichier);
        $img->setNomFichier($nomFichier);
        if ($type == File::TYPE_AVATAR){
            $img->setProfile($profile);
            $img->setType($type);
            $profile->addPhoto($img);
        }
    }

    #[Route('/supprime/file/{id}', name: 'profile_delete_files', methods: ['DELETE'])]
    public function deleteImage(File $file, Request $request): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if ($this->isCsrfTokenValid('delete' . $file->getId(), $data['_token'])) {
            $nom = $file->getNom();
            unlink($this->getParameter('files_directory') . '/' . $nom);

            $em = $this->getDoctrine()->getManager();
            $em->remove($file);
            $em->flush();

            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }

}
