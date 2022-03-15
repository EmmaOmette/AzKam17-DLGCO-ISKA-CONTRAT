<?php

namespace App\Service\Contrat\Manager;

use App\Entity\Contrat\Contrat;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Workflow\WorkflowInterface;

class ApproveContrat
{
    /**
     * @var WorkflowInterface
     */
    private $demandeContratStateMachine;
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security, WorkflowInterface $demandeContratStateMachine, EntityManagerInterface $manager)
    {
        $this->demandeContratStateMachine = $demandeContratStateMachine;
        $this->manager = $manager;
        $this->security = $security;
    }

    public function call(Contrat $contrat)
    {
        //On vérifie si la transition est applicable
        if(
            $this
                ->demandeContratStateMachine
                ->can(
                    $contrat,
                    'valider_manager'
                )
        ){
            //Récupération de l'utilisateur
            $user = $this->security->getUser();
            //Si l'utilisateur est une instance de User
            if($user instanceof User){
                //Si l'utilisateur est manager dans le département de la demande
                if(
                    ($contrat->getDepartementInitiateur() === $user->getDepartement()) && $user->getIsManager()
                ){
                    $this->demandeContratStateMachine->apply($contrat, 'valider_manager');
                    $this->manager->flush();
                    return true;
                }

            }
        }
        //Si aucune verif n'est passée on retourne false
        return false;
    }
}