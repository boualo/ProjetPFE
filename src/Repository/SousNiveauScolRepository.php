<?php

namespace App\Repository;

use App\Entity\SousNiveauScol;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SousNiveauScol>
 *
 * @method SousNiveauScol|null find($id, $lockMode = null, $lockVersion = null)
 * @method SousNiveauScol|null findOneBy(array $criteria, array $orderBy = null)
 * @method SousNiveauScol[]    findAll()
 * @method SousNiveauScol[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SousNiveauScolRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SousNiveauScol::class);
    }

    public function save(SousNiveauScol $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SousNiveauScol $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return SousNiveauScol[] Returns an array of SousNiveauScol objects
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

//    public function findOneBySomeField($value): ?SousNiveauScol
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
