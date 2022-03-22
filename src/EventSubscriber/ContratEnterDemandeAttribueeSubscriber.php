<?php

namespace App\EventSubscriber;

use App\Entity\Contrat\Contrat;
use App\Entity\Contrat\NotificationsContrat;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class ContratEnterDemandeAttribueeSubscriber implements EventSubscriberInterface
{

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function onEnterred(Event $event)
    {
        /** @var Contrat $contrat **/
        $contrat = $event->getSubject();

        //Création de la notification
        $notif = (new NotificationsContrat())
            ->setLib(
                "Le contrat {$contrat->getId()} a été attribuée à l'agent {$contrat->getUserJuridique()->getUser()->getEmail()}."
            )->setContrat( $contrat )
            ->setColor(
                'info'
            );
        $this->manager->persist($notif);

        $this->manager->flush();
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.demande_contrat.entered.demande_attribuee' => 'onEnterred',
        ];
    }
}
