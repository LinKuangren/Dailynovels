<?php

namespace App\Tests\Unit;

use App\Entity\Novels;
use App\Entity\Rating;
use App\Entity\Traducteurs;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class NovelsTest extends KernelTestCase
{
    public function getEntity() : Novels
    {
        $traduct = static::getContainer()->get('doctrine.orm.entity_manager')->find(Traducteurs::class, 1);

        return (new Novels())->setTitle('test unitaire #1')
            ->setDescription('description du texte')
            ->setAuthor('description du texte')
            ->setTraducteurs($traduct)
            ->setVisibilitie(true)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable());
    }

    public function testEntityIsValid(): void
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();

        $novel = $this->getEntity();

        $errors = $container->get('validator')->validate($novel);

        $this->assertCount(0, $errors);
    }

    # Test du Title
    public function testInvalideTitle()
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();

        $novel = $this->getEntity();
        $novel->setTitle('');
        
        $errors = $container->get('validator')->validate($novel);

        $this->assertCount(2, $errors);
    }

    # Test de la description
    public function testInvalideDescription()
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();

        $novel = $this->getEntity();
        $novel->setDescription('');
        
        $errors = $container->get('validator')->validate($novel);

        $this->assertCount(0, $errors);
    }

    # Test de la l'auteur
    public function testInvalideAuthor()
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();

        $novel = $this->getEntity();
        $novel->setAuthor('');
        
        $errors = $container->get('validator')->validate($novel);

        $this->assertCount(1, $errors);
    }

    # Test de la visibilité
    public function testInvalideVisibilitie()
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();

        $novel = $this->getEntity();
        $novel->setVisibilitie(false);
        
        $errors = $container->get('validator')->validate($novel);

        $this->assertCount(0, $errors);
    }

    # Test de notation
    public function testGetRating()
    {
        $novel = $this->getEntity();
        $user = static::getContainer()->get('doctrine.orm.entity_manager')->find(User::class, 1);

        for ($i=0; $i < 5; $i++) { 
            $rating = new Rating;
            $rating->setRating(2)
                ->setUser($user)
                ->setNovels($novel)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUpdatedAt(new \DateTimeImmutable());
            
            $novel->addRating($rating);
        }

        $this->assertTrue(2.0 === $novel->getAverage());
    }
}
