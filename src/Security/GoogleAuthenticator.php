<?php

namespace App\Security;

use App\Entity\User;
use App\Entity\File;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class GoogleAuthenticator extends OAuth2Authenticator
{
    use TargetPathTrait;

    private $clientRegistry;
    private $entityManager;
    private $router;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $entityManager, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }


    public function supports(Request $request): ?bool{

        return ($request->getPathInfo() == '/connect/google/check' && $request->isMethod('GET'));
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('google');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function() use ($accessToken, $client) {

                /** @var GoogleUser $googleUser */
                $googleUser = $client->fetchUserFromToken($accessToken);

                $googleId = $googleUser->getId();
                $email = $googleUser->getEmail();
                $prenom = $googleUser->getFirstName();
                $nom = $googleUser->getLastName();
                $picture = $googleUser->getAvatar();

                $emailVerified = $googleUser->toArray()['email_verified'];

                if ($emailVerified){

                    $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

                    $user = new User();
                    if ($existingUser) {
                        //return $existingUser;
                        $user = $existingUser;
                    }

                    $user->setEmail($email);
                    $user->setPassword('');
                    $user->setNom($nom);
                    $user->setPrenom($prenom);
                    $user->setIsVerified(true);
                    $user->setIsDeleted(0);
                    $user->setRoles(["ROLE_CANDIDAT"]);
                    $this->entityManager->persist($user);
                    if($picture){
                        $existingPicture = $this->entityManager->getRepository(File::class)->findOneBy(['nom' => $picture]);
                        if (!$existingPicture){
                            $file = new File();
                            $file->setType(File::TYPE_AVATAR);
                            $file->setNom($picture);
                            $file->setNomFichier($picture);
                            $file->setUser($user);
                            $this->entityManager->persist($file);
                        }
                    }

                    $this->entityManager->flush();
                    return $user;

                }

                $targetUrl = $this->router->generate('home');

                return new RedirectResponse($targetUrl);
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // change "app_homepage" to some route in your app
        $targetUrl = $this->router->generate('dashboard');

        return new RedirectResponse($targetUrl);

        // or, on success, let the request continue to be handled by the controller
        //return null;
    }
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): RedirectResponse {
        if ($request->hasSession()) {
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        }

        return new RedirectResponse($this->router->generate('login'));
    }

}
