<?php

namespace App\Repository;

use App\Entity\Traducteurs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Traducteurs>
 *
 * @method Traducteurs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Traducteurs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Traducteurs[]    findAll()
 * @method Traducteurs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TraducteursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Traducteurs::class);
    }

    public function paginationTraducteur()
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.id', 'ASC')
            ->getQuery()
        ;
    }

    public function findTraducteurNovel($traducteurId)
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.novels', 'n')
            ->where('t.id = :traducteurId')
            ->setParameter('traducteurId', $traducteurId)
            ->getQuery();
    }

//    /**
//     * @return Traducteurs[] Returns an array of Traducteurs objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Traducteurs
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
