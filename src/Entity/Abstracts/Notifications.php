<?php


namespace App\Entity\Abstracts;

use App\Repository\Abstracts\NotificationsRepository;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorMap;

/**
 * @ORM\Table (name="t_notifications")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @DiscriminatorMap({
 *     "notif_base" = "Notifications",
 *     "notif_contrat" = "App\Entity\Contrat\NotificationsContrat"
 * })
 * @ORM\Entity(repositoryClass=NotificationsRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Notifications
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $lib;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $color;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLib(): ?string
    {
        return $this->lib;
    }

    public function setLib(string $lib): self
    {
        $this->lib = $lib;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist
     * @return $this
     */
    public function setCreatedAt(): self
    {
        $this->createdAt = CarbonImmutable::now();

        return $this;
    }
}
