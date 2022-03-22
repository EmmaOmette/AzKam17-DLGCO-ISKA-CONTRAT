<?php

namespace App\EventSubscriber;

use App\Entity\Contrat\Contrat;
use App\Entity\Contrat\NotificationsContrat;
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

        //Création de la notification
        $notif = (new NotificationsContrat())
            ->setLib(
                "Le contrat {$contrat->getId()} a été validé par le manager. Elle est en attente d'attribution."
            )->setContrat( $contrat )
            ->setColor(
                'success'
            );
        $this->manager->persist($notif);

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