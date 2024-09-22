<?php

namespace App\Tests\Unit;

use App\Entity\Categories;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CategorieTest extends KernelTestCase
{
    public function getEntity() : Categories
    {
        return (new Categories())->setName('test unitaire #1')
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable());
    }

    public function testEntityIsValid(): void
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();

        $categorie = $this->getEntity();

        $errors = $container->get('validator')->validate($categorie);

        $this->assertCount(0, $errors);
    }

    public function testInvalideName()
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();

        $categorie = $this->getEntity();
        $categorie->setName('');
        
        $errors = $container->get('validator')->validate($categorie);

        $this->assertCount(2, $errors);
    }
}
