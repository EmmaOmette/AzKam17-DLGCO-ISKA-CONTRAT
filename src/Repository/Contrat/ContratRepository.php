<?php

namespace App\Repository\Contrat;

use App\Entity\Contrat\Contrat;
use App\Entity\Departement;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Contrat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contrat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contrat[]    findAll()
 * @method Contrat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContratRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contrat::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Contrat $entity, bool $flush = true): void
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
    public function remove(Contrat $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    //Obtenir les demandes d'un departement en fonction de leur statut
    public function getDepartementContrat(Departement $departement, string $statut = ""){
        $sql = $this
            ->createQueryBuilder('c')
            ->join('c.departementInitiateur', 'd')
            ->where('d = :departement')
            ->setParameter('departement', $departement);

        if(!empty($statut)){
            $sql
                ->andWhere('c.currentState = :statut')
                ->setParameter('statut', $statut);
        }

        return $sql
            ->getQuery()
            ->getResult()
            ;
    }

    //Compter le nombre de demandes en cours d'un contrat
    public function countContratPerDepartement(Departement $departement, string $statut = "")
    {
        $sql = $this
            ->createQueryBuilder('c')
            ->select('count(c.id) as total')
            ->join('c.departementInitiateur', 'd')
            ->where('d = :departement')
            ->setParameter('departement', $departement);

        if(!empty($statut)){
            $sql
                ->andWhere('c.currentState = :statut')
                ->setParameter('statut', $statut);
        }

        return $sql
            ->getQuery()
            ->getSingleResult()
            ;
    }

    //Listing des demandes non attribuees
    public function getContratParStatut(string $statut = "demande_non_attribuee")
    {
        return
            $this->createQueryBuilder('c')
            ->where('c.currentState = :currentState')
            ->setParameter('currentState', $statut)
            ->getQuery()
            ->getResult()
            ;
    }

    //Obtenir les demandes de contrat d'un agent juridique
    public function getDemandesAgentJuridique(User $user, string $statut = ""){
        $sql = $this
            ->createQueryBuilder('c')
            ->join('c.userJuridique', 'u')
            ->where('u.user = :user')
            ->setParameter('user', $user);


        if(!empty($statut)){
            $sql
                ->andWhere('c.currentState = :statut')
                ->setParameter('statut', $statut);
        }

        return $sql
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Contrat[] Returns an array of Contrat objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Contrat
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findMyContrats(User $user)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.agentInitiateur = :val')
            ->setParameter('val', $user)
            ->getQuery()
            ->getResult()
            ;
    }

    public function nbrDemandesTraites(User $user)
    {
        return $this->createQueryBuilder('c')
            ->select('SUM(u.nbrDemandesValidees + u.nbrDemandesRefusees)')
            ->join('c.userJuridique', 'u')
            ->where('c.userJuridique = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

    public function nbrEnAttenteValidation(User $user)
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.userJuridique = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }
}
