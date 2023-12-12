<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\Team;
use App\Entity\User;
use App\Form\TeamType;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/team')]
class TeamController extends AbstractController
{
    private TeamRepository $teamRepository;
    private EntityManagerInterface $manager;

    public function __construct(TeamRepository $teamRepository, EntityManagerInterface $entityManagerInterface){
        $this->teamRepository = $teamRepository;
        $this->manager = $entityManagerInterface;
    }

    #[Route('/', name: 'team_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MENTOR');

        $teamList = $this->teamRepository->findAll();
        $userList = $this->manager->getRepository(User::class)->findBy(['team' => null]);

        return $this->render('team/index.html.twig', [
            'team_list' => $teamList,
            'user_list' => $userList
        ]);
    }

    #[Route('/team/store', name: 'store_team', methods: 'POST')]
    public function store(Request $request): Response
    {
        $team = new Team();
        $team->setNom($request->get('nom'));
        $team->setPrenom($request->get('prenom'));
        $team->setRole($request->get('role'));
        $this->manager->persist($team);
        $this->manager->flush();
        return new Response('created');
    }

    #[Route('/team/get/{id}', name: 'get_team_data', methods: 'GET')]
    public function getTeamData($id) : JsonResponse{
        $team = $this->teamRepository->find($id);
        $team_data = [
            'id' => $team->getId(),
            'nom' => $team->getNom(),
            'prenom' => $team->getPrenom(),
            'role' => $team->getRole(),
            'members' => []
        ];
        foreach($team->getUsers() as $user){
            $team_data['members'][] = [
                'id' => $user->getId(),
                'full_name' => $user->getNom() . ' ' . $user->getPrenom()
            ];
        }

        return new JsonResponse($team_data);
    }

    #[Route('/team/{id}/add-member/{user_id}', name: 'add_member', methods: 'POST')]
    public function addMember($id, $user_id) : JsonResponse{
        $user = $this->manager->getRepository(User::class)->find($user_id);
        $team = $this->teamRepository->find($id);
        $user->setTeam($team);
        $this->manager->flush();
        $user_data = [
            'id' => $user->getId(),
            'full_name' => $user->getPrenom() . ' ' . $user->getNom()
        ];
        return new JsonResponse($user_data);
    }

    #[Route('/team/remove-member/{user_id}', name: 'remove_member', methods: 'POST')]
    public function removeMember($user_id) : Response{
        $user = $this->manager->getRepository(User::class)->find($user_id);
        $user->setTeam(null);
        $this->manager->flush();
        return new Response('deleted');
    }

    #[Route('/teams/{id}/update', name: 'update_team', methods: 'POST')]
    public function update(Request $request, $id) : Response{
        $team = $this->teamRepository->find($id);
        $team->setNom($request->get('nom'));
        $team->setPrenom($request->get('prenom'));
        $team->setRole($request->get('role'));
        $this->manager->flush();
        return new Response('updated');
    }


    #[Route('/team/{id}/destroy', name: 'destroy_team', methods: 'POST')]
    public function destroy($id): Response{
        $team = $this->teamRepository->find($id);
        $this->manager->remove($team);
        $this->manager->flush();
        return new Response('deleted');
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
