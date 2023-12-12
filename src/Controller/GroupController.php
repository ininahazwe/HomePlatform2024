<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Messages;
use App\Entity\User;
use App\Entity\Email;
use App\Form\createGroupUsersType;
use App\Form\GroupType;
use App\Form\MessagesType;
use App\Repository\GroupRepository;
use App\Repository\MessagesRepository;
use App\Repository\UserRepository;
use App\Service\Mailer;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validation;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\ArrayLoader;

#[Route('/cms/group')]
class GroupController extends AbstractController
{
    private GroupRepository $groupRepository;
    private EntityManagerInterface $manager;

    public function __construct(GroupRepository $groupRepository, EntityManagerInterface $entityManagerInterface){
        $this->groupRepository = $groupRepository;
        $this->manager = $entityManagerInterface;
    }

    #[Route('/', name: 'group_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MENTOR');

        $groupList = $this->groupRepository->findAll();
        $userList = $this->manager->getRepository(User::class)->findBy(['group' => null]);

        return $this->render('group/index.html.twig', [
            'group_list' => $groupList,
            'user_list' => $userList
        ]);
    }

    #[Route('/group/store', name: 'store_group', methods: 'POST')]
    public function store(Request $request): Response
    {
        $group = new Group();
        $group->setNom($request->get('nom'));
        $group->setDescription($request->get('description'));

        $this->manager->persist($group);
        $this->manager->flush();
        return new Response('created');
    }

    #[Route('/group/get/{id}', name: 'get_group_data', methods: 'GET')]
    public function getGroupData($id) : JsonResponse{
        $group = $this->groupRepository->find($id);
        $group_data = [
            'id' => $group->getId(),
            'nom' => $group->getNom(),
            'description' => $group->getDescription(),
            'members' => []
        ];
        foreach($group->getMembers() as $user){
            $group_data['members'][] = [
                'id' => $user->getId(),
                'full_name' => $user->getNom() . ' ' . $user->getPrenom()
            ];
        }

        return new JsonResponse($group_data);
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

    #[Route('/new', name: 'group_new', methods: ['GET','POST'])]
    public function new(Request $request): Response
    {
        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($group);
            $entityManager->flush();

            return $this->redirectToRoute('group_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('group/new.html.twig', [
            'group' => $group,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'group_show', methods: ['GET'])]
    public function show(Group $group): Response
    {
        return $this->render('group/show.html.twig', [
            'group' => $group,
        ]);
    }

    #[Route('/{id}/edit', name: 'group_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Group $group): Response
    {
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('group_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('group/edit.html.twig', [
            'group' => $group,
            'form' => $form,
        ]);
    }

    /**
     * @param Request $request
     * @param Group $group
     * @return Response
     */
    #[Route('/{id}', name: 'group_delete', methods: ['POST'])]
    public function delete(Request $request, Group $group): Response
    {
        if ($this->isCsrfTokenValid('delete'.$group->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($group);
            $entityManager->flush();
        }

        return $this->redirectToRoute('group_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @param Request $request
     * @param Mailer $mailer
     * @param Group $group
     * @param ManagerRegistry $doctrine
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     */
    #[Route('/{id}/mail', name: 'group_mail', methods: ['GET','POST'])]
    public function mail(Request $request, Mailer $mailer, Group $group, ManagerRegistry $doctrine): Response
    {
        /** @var  User $user */
        $user = $this->getUser();
        $members = $group->getMembers();
        $message = new Messages;
        $form = $this->createForm(MessagesType::class, $message, [
            'user' => $user
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            foreach($members as $member) {
                $message->setSender($user);
                $message->setRecipient($member);
                $message->setType(Messages::TYPE_GROUP);
                $em = $doctrine->getManager();
                $em->persist($message);
                $em->flush();

                $email = $em->getRepository(Email::class)->findOneBy(['code' => 'EMAIL_GROUP']);

                $loader = new ArrayLoader([
                    'email' => $email->getContenu(),
                ]);

                $twig = new Environment($loader);
                $messageMail = $twig->render('email', ['user' => $this->getUser(), 'messageMail' => $message->getMessage(), 'title' => $message->getTitle()]);

                $mailer->send([
                    'recipient_email' => $message->getRecipient()->getEmail(),
                    'subject' => $message->getTitle(),
                    'html_template' => 'email/email_vide.html.twig',
                    'context' => [
                        'message' => $messageMail
                    ]
                ]);
            }
            $this->addFlash('success', 'Successfully sent.');

            return $this->redirectToRoute('group_show', ['slug' => $group->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->render("group/envoi_mail.html.twig", [
            'form' => $form->createView(),
            'group' => $group
        ]);
    }

    #[Route('/historique/{id}', name: 'historique')]
    public function historiqueMessages(Request $request, Group $group, MessagesRepository $messagesRepository, ManagerRegistry $doctrine): Response
    {
        $recipient = $group->getId();
        $messages = $messagesRepository->findAll();
        return $this->render('group/historique.html.twig', [
            'group' => $group,
            'messages' => $messages
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/users/import/{id}', name: 'users_import')]
    public function usersImport(Request $request, Group $group, UserRepository $userRepository, ManagerRegistry $doctrine, UserPasswordHasherInterface $userPasswordHasherInterface, Mailer $mailer): Response
    {
        $file = new User();
        $form = $this->createForm(createGroupUsersType::class, $file);
        $form->handleRequest($request);

        $em = $doctrine->getManager();

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('fichier')->getData();
            /** @var UploadedFile */

            if (($handle = fopen($file->getPathname(), "r")) !== false) {
                $data = fgetcsv($handle, 100, ";");

                while (($data = fgetcsv($handle, 1000, ";")) !== false) {
                    $civilite = $data[0];
                    $prenom = utf8_encode($data[1]);
                    $nom = utf8_encode($data[2]);
                    $email = utf8_encode($data[3]);
                    if ($email) {
                        $validator = Validation::createValidator();
                        $errors = $validator->validate(
                            $email,
                            [
                                new \Symfony\Component\Validator\Constraints\Email([
                                    'message' => 'L\'adresse email {{ value }} n\'est pas valide.'
                                ])
                            ]
                        );

                        if (count($errors) === 0) {

                            $password = $userRepository->genererMDP();

                            $role = ['ROLE_CANDIDAT'];

                            $civi = User::GENRE_FEMME;

                            if ($civilite == 'homme') {
                                $civi = User::GENRE_HOMME;
                            }

                            if ($civilite == 'autre') {
                                $civi = User::GENRE_AUTRE;
                            }

                            $user = new User();

                            $userExist = $userRepository->findOneBy(['email' => $email]);

                            if ($userExist) {
                                $em->persist($userExist);
                            }

                            if (!$userExist) {

                                $user->setCivilite($civi);
                                $user->setNom($nom);
                                $user->setPrenom($prenom);
                                $user->setEmail($email);
                                $user->setRoles($role);
                                $user->addGroup($group);
                                $user->setIsVerified(User::ACCEPTEE);
                                $user->setPassword(
                                    $userPasswordHasherInterface->hashPassword(
                                        $user,
                                        $password
                                    )
                                );

                                $email = $em->getRepository(\App\Entity\Email::class)->findOneBy(['code' => 'EMAIL_CREATION_RECRUTEUR']);
                                if ($email) {
                                    $loader = new ArrayLoader([
                                        'email' => $email->getContenu(),
                                    ]);

                                    $twig = new Environment($loader);
                                    $message = $twig->render('email', ['user' => $this->getUser(), 'candidat' => $user, 'password' => $password]);

                                    $mailer->send([
                                        'from' => null,
                                        'recipient_email' => $user->getEmail(),
                                        'subject' => $email->getSujet(),
                                        'html_template' => 'email/email_vide.html.twig',
                                        'context' => [
                                            'message' => $message
                                        ]
                                    ]);

                                }

                                $em->persist($user);
                            }
                            $em->flush();
                        }
                    }
                }
                fclose($handle);
                $em->flush();
            }

            $this->addFlash('success', 'Added successfully');

            if ($referer = $request->get('referer', false)) {
                $referer = base64_decode($referer);
                return $this->redirect(($referer));
            } else {
                return $this->redirectToRoute('group_show',['slug' => $group->getSlug()], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('group/import_users.html.twig', [
            'group' => $group,
            'form' => $form->createView(),
        ]);
    }
}
