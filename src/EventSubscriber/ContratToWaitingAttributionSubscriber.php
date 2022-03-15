<?php

namespace App\EventSubscriber;

use App\Entity\Contrat\Contrat;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Event\GuardEvent;

class ContratToWaitingAttributionSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function onEnterred(Event $event){
        /** @var Contrat $contrat **/
        $contrat = $event->getSubject();

        $contrat->setCurrentState('demande_non_attribuee');
        $this->manager->persist($contrat);
        $this->manager->flush();
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            'workflow.demande_contrat.entered.demande_acceptee_manager' => ['onEnterred']
        ];
    }
}