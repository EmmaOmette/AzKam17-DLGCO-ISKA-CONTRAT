<?php

namespace App\Entity\Obligation;

use App\Repository\Obligation\ObligationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ObligationRepository::class)
 */
class Obligation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $sourceDisposition;

    /**
     * @ORM\Column(type="text")
     */
    private $reference;

    /**
     * @ORM\Column(type="text")
     */
    private $pointsAbordes;

    /**
     * @ORM\Column(type="text")
     */
    private $obligation;

    /**
     * @ORM\Column(type="text")
     */
    private $sanctionsPrevuesImpact;

    /**
     * @ORM\ManyToOne(targetEntity=StatutObligation::class, inversedBy="obligations")
     */
    private $statut;

    /**
     * @ORM\ManyToOne(targetEntity=ImportanceObligation::class, inversedBy="obligations")
     */
    private $importanceObligation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSourceDisposition(): ?string
    {
        return $this->sourceDisposition;
    }

    public function setSourceDisposition(string $sourceDisposition): self
    {
        $this->sourceDisposition = $sourceDisposition;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getPointsAbordes(): ?string
    {
        return $this->pointsAbordes;
    }

    public function setPointsAbordes(string $pointsAbordes): self
    {
        $this->pointsAbordes = $pointsAbordes;

        return $this;
    }

    public function getObligation(): ?string
    {
        return $this->obligation;
    }

    public function setObligation(string $obligation): self
    {
        $this->obligation = $obligation;

        return $this;
    }

    public function getSanctionsPrevuesImpact(): ?string
    {
        return $this->sanctionsPrevuesImpact;
    }

    public function setSanctionsPrevuesImpact(string $sanctionsPrevuesImpact): self
    {
        $this->sanctionsPrevuesImpact = $sanctionsPrevuesImpact;

        return $this;
    }

    public function getStatut(): ?StatutObligation
    {
        return $this->statut;
    }

    public function setStatut(?StatutObligation $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getImportanceObligation(): ?ImportanceObligation
    {
        return $this->importanceObligation;
    }

    public function setImportanceObligation(?ImportanceObligation $importanceObligation): self
    {
        $this->importanceObligation = $importanceObligation;

        return $this;
    }
}
