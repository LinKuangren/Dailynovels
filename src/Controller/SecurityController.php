<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;

class SecurityController extends AbstractController
{
    public const SCOPES = [
        'google' => [],
    ];

    #[Route('/connexion', name: 'security_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('accueil');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        $user = $this->getUser();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'user' => $user,
        ]);
    }

    #[Route("/connect/{service}", name: 'google_connect', methods: ['GET'])]
    public function connect(string $service, ClientRegistry $clientRegistry): RedirectResponse
    {
        if (! in_array($service, array_keys(self::SCOPES), true)) {
            throw $this->createNotFoundException();
        }

        return $clientRegistry
            ->getClient($service)
            ->redirect(self::SCOPES[$service]);
    }

    #[Route('/check/{service}', name: 'auth_oauth_check', methods: ['GET', 'POST'])]
    public function check(): Response
    {
        return new Response(status: 200);
    }

    #[Route(path: '/deconnexion', name: 'security_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/inscription', 'security_registration', methods: ['GET', 'POST'])]
    public function registration(Request $request, PersistenceManagerRegistry $doctrine): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('accueil');
        }

        $user = new User();
        $user->setRoles(['ROLE_USER']);

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        $defaultImage = 'inconnu.jpg';

        if ($form->isSubmitted() && $form->isValid()) {
            $users = $form->getData();

            if (!$users->getImageName()) {
                $users->setImageName($defaultImage);
            }

            $this->addFlash(
                'success_inscription',
                'Votre compte a bien été créé.'
            );

            $em = $doctrine->getManager();
            $em->persist($users);
            $em->flush();

            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
