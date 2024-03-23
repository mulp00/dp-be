<?php

namespace App\Repository;

use App\Entity\MFKDFPolicy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MFKDFPolicy>
 *
 * @method MFKDFPolicy|null find($id, $lockMode = null, $lockVersion = null)
 * @method MFKDFPolicy|null findOneBy(array $criteria, array $orderBy = null)
 * @method MFKDFPolicy[]    findAll()
 * @method MFKDFPolicy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MFKDFPolicyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MFKDFPolicy::class);
    }

    //    /**
    //     * @return MFKDFPolicy[] Returns an array of MFKDFPolicy objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?MFKDFPolicy
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
