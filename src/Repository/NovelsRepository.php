<?php

namespace App\Repository;

use App\Entity\Novels;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @extends ServiceEntityRepository<Novels>
 *
 * @method Novels|null find($id, $lockMode = null, $lockVersion = null)
 * @method Novels|null findOneBy(array $criteria, array $orderBy = null)
 * @method Novels[]    findAll()
 * @method Novels[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NovelsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Novels::class);
    }

    public function findByStartingLetter($letter)
    {
        return $this->createQueryBuilder('n')
            ->Where('n.title LIKE :letter')
            ->setParameter('letter', $letter . '%')
            ->orderBy('n.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByFavorisClassement()
    {
        return $this->createQueryBuilder('n')
            ->select('n, COUNT(u.id) as HIDDEN favorisCount')
            ->leftJoin('n.favoris', 'u')
            ->where('n.Visibilitie = :visibilitie')
            ->setParameter('visibilitie', true)
            ->groupBy('n')
            ->orderBy('favorisCount', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByChapitresClassement()
    {
        return $this->createQueryBuilder('n')
            ->select('n, COUNT(c.id) as HIDDEN chapitresCount')
            ->leftJoin('n.chapitres', 'c')
            ->where('n.Visibilitie = :visibilitie')
            ->setParameter('visibilitie', true)
            ->groupBy('n')
            ->orderBy('chapitresCount', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByDateCreationClassement()
    {
        return $this->createQueryBuilder('n')
            ->where('n.Visibilitie = :visibilitie')
            ->setParameter('visibilitie', true)
            ->orderBy('n.CreatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findRomansByAdvancedSearch($recherche)
    {
        $queryBuilder = $this->createQueryBuilder('r');

        if ($recherche['categories']) {
            $queryBuilder
                ->leftJoin('r.categories', 'c')
                ->andWhere($queryBuilder->expr()->in('c.id', $recherche['categories']));
        }

        if ($recherche['tags']) {
            $queryBuilder
                ->leftJoin('r.tags', 't')
                ->andWhere($queryBuilder->expr()->in('t.id', $recherche['tags']));
        }

        if ($recherche['orderBy']) {
            switch ($recherche['orderBy']) {
                case 'favorites':
                    $queryBuilder->orderBy('r.favorites', 'DESC');
                    break;
                case 'recent':
                    $queryBuilder->orderBy('r.datePublication', 'DESC');
                    break;
                case 'chapters':
                    $queryBuilder->orderBy('r.chapters', 'DESC');
                    break;
            }
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function paginationNovels(): Query
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.id', 'ASC')
            ->getQuery()
        ;
    }

//    /**
//     * @return Novels[] Returns an array of Novels objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Novels
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
