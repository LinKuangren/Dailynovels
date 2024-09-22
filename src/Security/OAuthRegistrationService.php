<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class OAuthRegistrationService
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @param GoogleUser $resourceOwner
     */
    public function persist(ResourceOwnerInterface $resourceOwner, UserRepository $repository, string $accessToken): User
    {
        $user = (new User())
            ->setEmail($resourceOwner->getEmail())
            ->setPseudo($resourceOwner->getName())
            ->setGoogleId($resourceOwner->getId());

        $user->setRoles(['ROLE_USER']);

        $defaultImage = 'inconnu.jpg';
        if (!$user->getImageName()) {
            $user->setImageName($defaultImage);
        }

        $randomPassword = $this->generateRandomPassword();
        $hashedPassword = $this->passwordHasher->hashPassword($user, $randomPassword);
        $user->setPassword($hashedPassword);

        $repository->add($user, true);
        return $user;
    }

    private function generateRandomPassword(int $length = 12): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
