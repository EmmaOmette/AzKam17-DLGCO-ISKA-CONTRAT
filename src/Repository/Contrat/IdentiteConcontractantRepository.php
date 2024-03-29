<?php

namespace App\Repository\Contrat;

use App\Entity\Contrat\IdentiteConcontractant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method IdentiteConcontractant|null find($id, $lockMode = null, $lockVersion = null)
 * @method IdentiteConcontractant|null findOneBy(array $criteria, array $orderBy = null)
 * @method IdentiteConcontractant[]    findAll()
 * @method IdentiteConcontractant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IdentiteConcontractantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IdentiteConcontractant::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(IdentiteConcontractant $entity, bool $flush = true): void
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
    public function remove(IdentiteConcontractant $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return IdentiteConcontractant[] Returns an array of IdentiteConcontractant objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?IdentiteConcontractant
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
