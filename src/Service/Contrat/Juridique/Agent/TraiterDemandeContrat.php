<?php

namespace App\Service\Contrat\Juridique\Agent;

use App\Entity\Contrat\Contrat;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Workflow\WorkflowInterface;

class TraiterDemandeContrat
{
    /**
     * @var Security
     */
    private $security;
    /**
     * @var WorkflowInterface
     */
    private $demandeContratStateMachine;
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(Security $security, WorkflowInterface $demandeContratStateMachine, EntityManagerInterface $manager)
    {
        $this->security = $security;
        $this->demandeContratStateMachine = $demandeContratStateMachine;
        $this->manager = $manager;
    }

    public function call(Contrat $contrat, string $statut)
    {
        if($this->security->isGranted('ROLE_JURIDIQUE')){
            if(
                $this->demandeContratStateMachine->can($contrat, 'refuser_demande') || $this->demandeContratStateMachine->can($contrat, 'valider_demande')
            ){
                $statut = ($statut == "Approuvé") ? "valider_demande" : "refuser_demande";
                $contrat
                    ->setFintAttribution(
                        Carbon::now()
                    );
                $this->demandeContratStateMachine->apply($contrat, $statut);
                $this->manager->flush();
                return true;
            }
        }
        return false;
    }
}