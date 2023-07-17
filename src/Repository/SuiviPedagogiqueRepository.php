<?php

namespace App\Repository;

use App\Entity\SuiviPedagogique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SuiviPedagogique>
 *
 * @method SuiviPedagogique|null find($id, $lockMode = null, $lockVersion = null)
 * @method SuiviPedagogique|null findOneBy(array $criteria, array $orderBy = null)
 * @method SuiviPedagogique[]    findAll()
 * @method SuiviPedagogique[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SuiviPedagogiqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SuiviPedagogique::class);
    }

//    /**
//     * @return SuiviPedagogique[] Returns an array of SuiviPedagogique objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SuiviPedagogique
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
