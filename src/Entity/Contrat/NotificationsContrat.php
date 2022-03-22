<?php

namespace App\Entity\Contrat;

use App\Entity\Abstracts\Notifications;
use App\Repository\Contrat\NotificationsContratRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table (name="t_notifications_contrat")
 * @ORM\Entity(repositoryClass=NotificationsContratRepository::class)
 */
class NotificationsContrat extends Notifications
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Contrat::class, inversedBy="notifications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contrat;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContrat(): ?Contrat
    {
        return $this->contrat;
    }

    public function setContrat(?Contrat $contrat): self
    {
        $this->contrat = $contrat;

        return $this;
    }
}
