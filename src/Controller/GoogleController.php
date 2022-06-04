<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GoogleController extends AbstractController
{

    /**
     * @param ClientRegistry $clientRegistry
     * @return RedirectResponse
     */
    #[Route('/connect/google', name: 'connect_google')]
    public function connectAction(ClientRegistry $clientRegistry): RedirectResponse
    {
        return $clientRegistry
            ->getClient('google')
            ->redirect();
    }

    /**
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectCheckAction(Request $request): RedirectResponse|JsonResponse
    {
        //dd($request);
        if(!$this->getUser()){
            return new JsonResponse(array('status' => false, 'message' => "Utilisateur non trouvé"));
        } else {
            return $this->redirectToRoute('dashboard');
        }
    }
}
