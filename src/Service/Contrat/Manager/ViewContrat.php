<?php

namespace App\Service\Contrat\Manager;

use App\Entity\Contrat\Contrat;
use Symfony\Component\Workflow\WorkflowInterface;

class ViewContrat
{
    /**
     * @var WorkflowInterface
     */
    private $demandeContratStateMachine;

    public function __construct(WorkflowInterface $demandeContratStateMachine)
    {
        $this->demandeContratStateMachine = $demandeContratStateMachine;
    }

    public function call(Contrat $contrat){
        $result = [
            'waiting' => false,
            'denied' => false,
            'approved' => false,
        ];

        switch ($contrat->getCurrentState()){
            case "en_attente_manager":
                $result['waiting'] = true;
                break;
            case "demande_rejetee_manager":
                $result['denied'] = true;
                break;
            default:
                $result['approved'] = true;
        }

        $result['denied'] = !$result['approved'];

        return $result;
    }
}