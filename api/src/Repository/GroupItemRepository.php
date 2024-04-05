<?php

namespace App\Repository;

use App\Entity\GroupItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GroupItem>
 *
 * @method GroupItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupItem[]    findAll()
 * @method GroupItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupItem::class);
    }

    /**
     * @param string $targetGroupId
     * @return GroupItem[]
     */
    public function findByTargetGroupId(string $targetGroupId): array
    {
        return $this->createQueryBuilder('gi')
            ->innerJoin('gi.targetGroup', 'tg')
            ->where('tg.id = :targetGroupId')
            ->setParameter('targetGroupId', $targetGroupId)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return GroupItem[] Returns an array of GroupItem objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?GroupItem
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
