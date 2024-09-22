<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserNewpasswordType;
use App\Form\UserImageType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[Route('/profil/{pseudo}', name: 'profil', methods: ['GET', 'POST'])]
    public function index(User $user, Request $request, PersistenceManagerRegistry $doctrine, UploaderHelper $uploaderHelper, UserPasswordHasherInterface $hasher, EntityManagerInterface $manager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('security_login');
        }

        if ($this->getUser() !== $user) {
            return $this->redirectToRoute('accueil');
        }

        $formPassword = $this->createForm(UserNewpasswordType::class, $user);
        $formImage = $this->createForm(UserImageType::class, $user);

        $formPassword->handleRequest($request);
        $formImage->handleRequest($request);

        if ($formPassword->isSubmitted() && $formPassword->isValid()) {
            if ($hasher->isPasswordValid($user, $formPassword->get('password')->getData())) {
                $userRepository = $manager->getRepository(User::class);
                $my_user = $userRepository->findOneBy(['id'=> $this->getUser()->getId()]);

                $my_user->setPassword($formPassword->get('plainPassword')->getData());
                $user->setUpdatedAt(new \DateTimeImmutable());

                $manager->persist($my_user);
                $manager->flush();

                $this->addFlash(
                    'success_mdp',
                    'Le mot de passe de votre compte à bien été modifiées.'
                );

                return $this->redirectToRoute('profil', ['pseudo' => $user->getPseudo()]);
            } else {
                $this->addFlash(
                    'warning_mdp',
                    'Le mot de passe renseigné est incorrect.'
                );
            }
        }

        if ($formImage->isSubmitted() && $formImage->isValid()) {
            $uploadedFile = $formImage['imageFile']->getData();
            $user->setUpdatedAt(new \DateTimeImmutable());

            if ($uploadedFile) {
                $newFilename = $uploaderHelper->asset($user, 'imageFile');
                $user->setImageName($newFilename);
            }

            $entityManager = $doctrine->getManager();
            $entityManager->flush();

            $this->addFlash(
                'success_img',
                'L\'image de votre compte à bien été modifiées.'
            );

            return $this->redirectToRoute('profil', ['pseudo' => $user->getPseudo()]);
        } else {
            $this->addFlash(
                'warning_img',
                'Une erreur c\'est produite lors du changement d\'image.'
            );
        }

        return $this->render('user/profil.html.twig', [
            'formPassword' => $formPassword->createView(),
            'formImage' => $formImage->createView(),
        ]);
    }

    #[Route('/profil/{pseudo}/favoris', name: 'favoris')]
    public function favoris(User $user, UserRepository $userRepo, PersistenceManagerRegistry $doctrine, string $pseudo): Response
    {
        $users = $userRepo->findOneBy(['pseudo' => $pseudo]);

        if (!$this->getUser()) {
            return $this->redirectToRoute('security_login');
        }

        if ($this->getUser() !== $user) {
            return $this->redirectToRoute('accueil');
        }

        if ($users->getFavoris()->contains($users)) {
            // Retirez le roman des favoris de l'utilisateur
            $users->removeFavori($users);
        }

        $entityManager = $doctrine->getManager();
        $entityManager->persist($users);
        $entityManager->flush();

        return $this->render('user/favoris.html.twig', ['user' => $users]);
    }
}
