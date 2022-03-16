<?php

namespace App\Entity\Obligation;

use App\Repository\Obligation\ResponsableRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ResponsableRepository::class)
 */
class Responsable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lib;

    /**
     * @ORM\Column(type="boolean")
     */
    private $dispoDeroulante;

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

    public function getDispoDeroulante(): ?bool
    {
        return $this->dispoDeroulante;
    }

    public function setDispoDeroulante(bool $dispoDeroulante): self
    {
        $this->dispoDeroulante = $dispoDeroulante;

        return $this;
    }
}
