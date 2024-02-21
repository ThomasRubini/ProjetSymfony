<?php

namespace App\Repository;

use App\Entity\ApiDiscog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ApiDiscog>
 *
 * @method ApiDiscog|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApiDiscog|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApiDiscog[]    findAll()
 * @method ApiDiscog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApiDiscogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiDiscog::class);
    }

//    /**
//     * @return ApiDiscog[] Returns an array of ApiDiscog objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ApiDiscog
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
