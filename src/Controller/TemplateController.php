<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\FileRepository;
use App\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/template')]
class TemplateController extends AbstractController
{
    /**
     * @param Request $request
     * @param ProfileRepository $profileRepository
     * @return Response
     */
    #[Route('/dashboard/header', name: 'base_header', methods: ['GET'])]
    public function header(Request $request,ProfileRepository $profileRepository): Response
    {
        $user = null;
        $profile = null;
        if ($this->getUser()){
            /** @var User $user */
            $user = $this->getUser();
            $profile = $profileRepository->findOneBy(['user' => $user]);
        }

        return $this->render('layouts/header.html.twig', [
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    /**
     * @param Request $request
     * @param ProfileRepository $profileRepository
     * @param FileRepository $fileRepository
     * @return Response
     */
    #[Route('/dashboard/header', name: 'base_header_dashboard', methods: ['GET'])]
    public function headerDashBoard(Request $request,ProfileRepository $profileRepository, FileRepository $fileRepository): Response
    {
        $user = null;
        $profile = null;
        //$avatar = null;
        if ($this->getUser()){

            /** @var User $user */
            $user = $this->getUser();
            $profile = $profileRepository->findOneBy(['user' => $user]);

            //$avatar = $fileRepository->getAvatar($user, $profile);
        }

        return $this->render('dashboard/super-admin-sidebar.html.twig', [
            'user' => $user,
            'profile' => $profile,
            //'avatar' => $avatar,
        ]);
    }

    /**
     * @param Request $request
     * @param ProfileRepository $profileRepository
     * @return Response
     */
    #[Route('/dashboard/sidebar', name: 'base_sidebar_dashboard', methods: ['GET'])]
    public function sidebarDashBoard(Request $request,
                                     ProfileRepository $profileRepository,

    ): Response
    {
        $user = null;
        $profile = null;

        if ($this->getUser()){
            /** @var User $user */
            $user = $this->getUser();
            $profile = $profileRepository->findOneBy(['user' => $user]);
        }

        return $this->render('dashboard/sidebar.html.twig', [
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    /**
     * @param Request $request
     * @param ProfileRepository $profileRepository
     * @return Response
     */
    #[Route('/nav', name: 'base_nav', methods: ['GET'])]
    public function navDashBoard(Request $request,ProfileRepository $profileRepository): Response
    {
        $user = null;
        $profile = null;
        if ($this->getUser()){
            /** @var User $user */
            $user = $this->getUser();
            $profile = $profileRepository->findOneBy(['user' => $user]);
        }

        return $this->render('layouts/allnav.html.twig', [
            'user' => $user,
            'profile' => $profile,
        ]);
    }
}
