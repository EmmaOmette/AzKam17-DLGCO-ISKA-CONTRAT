<?php

namespace App\Entity;

use App\Entity\Contrat\Contrat;
use App\Repository\UserJuridiqueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserJuridiqueRepository::class)
 */
class UserJuridique
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
    private $tempsTraitement;

    /**
     * @ORM\OneToMany(targetEntity=Contrat::class, mappedBy="userJuridique")
     */
    private $contrats;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbrDemandesValidees;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbrDemandesRefusees;

    public function __construct()
    {
        $this->contrats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTempsTraitement(): ?string
    {
        return $this->tempsTraitement;
    }

    public function setTempsTraitement(string $tempsTraitement): self
    {
        $this->tempsTraitement = $tempsTraitement;

        return $this;
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
            $contrat->setUserJuridique($this);
        }

        return $this;
    }

    public function removeContrat(Contrat $contrat): self
    {
        if ($this->contrats->removeElement($contrat)) {
            // set the owning side to null (unless already changed)
            if ($contrat->getUserJuridique() === $this) {
                $contrat->setUserJuridique(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getNbrDemandesValidees(): ?int
    {
        return $this->nbrDemandesValidees;
    }

    public function setNbrDemandesValidees(?int $nbrDemandesValidees): self
    {
        $this->nbrDemandesValidees = $nbrDemandesValidees;

        return $this;
    }

    public function getNbrDemandesRefusees(): ?int
    {
        return $this->nbrDemandesRefusees;
    }

    public function setNbrDemandesRefusees(?int $nbrDemandesRefusees): self
    {
        $this->nbrDemandesRefusees = $nbrDemandesRefusees;

        return $this;
    }
}
