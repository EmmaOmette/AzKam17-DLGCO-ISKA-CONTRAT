<?php

namespace App\Service\Contrat\Juridique\Manager;

use App\Entity\Contrat\Contrat;
use App\Entity\UserJuridique;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\WorkflowInterface;

class AttribuerDemandeAgent
{
    /**
     * @var WorkflowInterface
     */
    private $demandeContratStateMachine;
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(WorkflowInterface $demandeContratStateMachine, EntityManagerInterface $manager)
    {
        $this->demandeContratStateMachine = $demandeContratStateMachine;
        $this->manager = $manager;
    }

    public function call(Contrat $contrat){
        if($this->demandeContratStateMachine->can($contrat, 'attribuer')){

            $contrat->setDebutAttribution(
                Carbon::now()
            );

            $this->demandeContratStateMachine->apply($contrat, 'attribuer');
            $this->manager->persist($contrat);
            $this->manager->flush();
            return true;
        }
        return false;
    }
}