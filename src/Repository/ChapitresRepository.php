<?php

namespace App\Repository;

use App\Entity\Chapitres;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chapitres>
 *
 * @method Chapitres|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chapitres|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chapitres[]    findAll()
 * @method Chapitres[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChapitresRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chapitres::class);
    }

    // public function findLatestChapterForNovel($novel)
    // {
    //     return $this->createQueryBuilder('c')
    //         ->where('c.novels = :novels')
    //         ->setParameter('novels', $novel)
    //         ->orderBy('c.CreatedAt', 'DESC')
    //         ->setMaxResults(1)
    //         ->getQuery()
    //         ->getOneOrNullResult();
    // }

//    /**
//     * @return Chapitres[] Returns an array of Chapitres objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Chapitres
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
