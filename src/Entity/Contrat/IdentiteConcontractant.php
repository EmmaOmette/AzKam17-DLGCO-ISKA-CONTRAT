<?php

namespace App\Entity\Contrat;

use App\Entity\Abstracts\Statut;
use App\Repository\Contrat\IdentiteConcontractantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=IdentiteConcontractantRepository::class)
 */
class IdentiteConcontractant extends Statut
{
    /**
     * @ORM\OneToMany(targetEntity=Contrat::class, mappedBy="identiteConcontractant")
     */
    private $contrats;

    public function __construct()
    {
        $this->contrats = new ArrayCollection();
    }

    /**
     * @return Collection<int, Contrat>
     */
    public function getContrats(): Collection
    {
        return $this->contrats;
    }

    public function addContrat(Contrat $contrat): self
    {
        if (!$this->contrats->contains($contrat)) {
            $this->contrats[] = $contrat;
            $contrat->setIdentiteConcontractant($this);
        }

        return $this;
    }

    public function removeContrat(Contrat $contrat): self
    {
        if ($this->contrats->removeElement($contrat)) {
            // set the owning side to null (unless already changed)
            if ($contrat->getIdentiteConcontractant() === $this) {
                $contrat->setIdentiteConcontractant(null);
            }
        }

        return $this;
    }
}
