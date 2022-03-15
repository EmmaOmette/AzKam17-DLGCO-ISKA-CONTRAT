<?php

namespace App\Service\Contrat\Juridique;

use App\Entity\User;
use App\Repository\Contrat\ContratRepository;
use Symfony\Component\Security\Core\Security;

class GetMesDemandes
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

    public function call($statut = ""){
        /* @var User $user */
        $user = $this->security->getUser();
        return $this->contratRepository->getDemandesAgentJuridique($user, $statut);
    }
}