<?php

namespace App\Entity\AvisConseils;

use App\Repository\AvisConseils\AvisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;

/**
 * @Table(name = "t_avis")
 * @ORM\Entity(repositoryClass=AvisRepository::class)
 */
class Avis
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
    private $objet;

    /**
     * @ORM\Column(type="text")
     */
    private $rensignement;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $niveauExecution;

    /**
     * @ORM\OneToMany(targetEntity=DocAvisConseils::class, mappedBy="avis")
     */
    private $docAvisConseils;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $currentState;

    public function __construct()
    {
        $this->docAvisConseils = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getObjet(): ?string
    {
        return $this->objet;
    }

    public function setObjet(string $objet): self
    {
        $this->objet = $objet;

        return $this;
    }

    public function getRensignement(): ?string
    {
        return $this->rensignement;
    }

    public function setRensignement(string $rensignement): self
    {
        $this->rensignement = $rensignement;

        return $this;
    }

    public function getNiveauExecution(): ?string
    {
        return $this->niveauExecution;
    }

    public function setNiveauExecution(?string $niveauExecution): self
    {
        $this->niveauExecution = $niveauExecution;

        return $this;
    }

    /**
     * @return Collection<int, DocAvisConseils>
     */
    public function getDocAvisConseils(): Collection
    {
        return $this->docAvisConseils;
    }

    public function addDocAvisConseil(DocAvisConseils $docAvisConseil): self
    {
        if (!$this->docAvisConseils->contains($docAvisConseil)) {
            $this->docAvisConseils[] = $docAvisConseil;
            $docAvisConseil->setAvis($this);
        }

        return $this;
    }

    public function removeDocAvisConseil(DocAvisConseils $docAvisConseil): self
    {
        if ($this->docAvisConseils->removeElement($docAvisConseil)) {
            // set the owning side to null (unless already changed)
            if ($docAvisConseil->getAvis() === $this) {
                $docAvisConseil->setAvis(null);
            }
        }

        return $this;
    }

    public function getCurrentState(): ?string
    {
        return $this->currentState;
    }

    public function setCurrentState(string $currentState): self
    {
        $this->currentState = $currentState;

        return $this;
    }
}
