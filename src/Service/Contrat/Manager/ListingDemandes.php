<?php

namespace App\Service\Contrat\Manager;

use App\Entity\Departement;
use App\Repository\Contrat\ContratRepository;
use Symfony\Component\Security\Core\Security;

class ListingDemandes
{
    /**
     * @var Security
     */
    private $security;
    /**
     * @var ContratRepository
     */
    private $contratRepository;

    public function __construct(Security $security, ContratRepository $contratRepository)
    {
        $this->security = $security;
        $this->contratRepository = $contratRepository;
    }

    public function call($etat = ''){
        //On vérifie si l'utilisateur est manager
        if($this->security->isGranted('ROLE_USER_MANAGER')){
            //L'utilisateur est manager on peut prendre les demandes de sont departements
            $departement = $this->security->getUser()->getDepartement();

            if($departement instanceof Departement){
                return $this->contratRepository->getDepartementContrat(
                    $this->security->getUser()->getDepartement(),
                    $etat
                );
            }
        }
        return false;
    }
}