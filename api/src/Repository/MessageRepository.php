<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 *
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * Finds the message with the highest epoch number for a given group UUID.
     *
     * @param string $groupId The UUID of the group.
     * @return Message|null The message with the highest epoch number or null if no message is found.
     * @throws NonUniqueResultException
     */
    public function findLatestMessageByGroupId(string $groupId): ?Message
    {
        $qb = $this->createQueryBuilder('m');
        $qb->where('m.targetGroup = :groupId')
            ->setParameter('groupId', $groupId)
            ->orderBy('m.epoch', 'DESC')
            ->setMaxResults(1);

        $query = $qb->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * Finds messages with an epoch number greater than the specified epoch for a given group UUID.
     *
     * @param string $groupId The UUID of the group.
     * @param int $epoch The epoch number to compare against.
     * @return Message[] The messages with an epoch number greater than the specified epoch, sorted from lowest to highest epoch.
     */
    public function findMessagesByGroupIdWithMinEpoch(string $groupId, int $epoch): array
    {
        $qb = $this->createQueryBuilder('m');
        $qb->where('m.targetGroup = :groupId')
            ->andWhere('m.epoch > :epoch')
            ->setParameter('groupId', $groupId)
            ->setParameter('epoch', $epoch)
            ->orderBy('m.epoch', 'ASC');

        $query = $qb->getQuery();

        return $query->getResult();
    }

    //    /**
    //     * @return Message[] Returns an array of Message objects
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

    //    public function findOneBySomeField($value): ?Message
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
