<?php

namespace App\Repository\Contrat;

use App\Entity\Contrat\NotificationsContrat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NotificationsContrat|null find($id, $lockMode = null, $lockVersion = null)
 * @method NotificationsContrat|null findOneBy(array $criteria, array $orderBy = null)
 * @method NotificationsContrat[]    findAll()
 * @method NotificationsContrat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationsContratRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotificationsContrat::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(NotificationsContrat $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(NotificationsContrat $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return NotificationsContrat[] Returns an array of NotificationsContrat objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NotificationsContrat
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
