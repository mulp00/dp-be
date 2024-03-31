<?php

namespace App\Repository;

use App\Entity\SerializedUserGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SerializedUserGroup>
 *
 * @method SerializedUserGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method SerializedUserGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method SerializedUserGroup[]    findAll()
 * @method SerializedUserGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SerializedUserGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SerializedUserGroup::class);
    }

//    /**
//     * @return SerializedUserGroup[] Returns an array of SerializedUserGroup objects
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

//    public function findOneBySomeField($value): ?SerializedUserGroup
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
