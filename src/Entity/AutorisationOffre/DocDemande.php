<?php

namespace App\Entity\AutorisationOffre;

use App\Entity\Abstracts\Document;
use App\Repository\AutorisationOffre\DocDemandeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DocDemandeRepository::class)
 */
class DocDemande extends Document
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Demande::class, inversedBy="documents")
     * @ORM\JoinColumn(nullable=false)
     */
    private $demande;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDemande(): ?Demande
    {
        return $this->demande;
    }

    public function setDemande(?Demande $demande): self
    {
        $this->demande = $demande;

        return $this;
    }
}
