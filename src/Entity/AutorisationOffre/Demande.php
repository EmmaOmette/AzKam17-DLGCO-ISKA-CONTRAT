<?php

namespace App\Entity\AutorisationOffre;

use App\Entity\User;
use App\Repository\AutorisationOffre\DemandeRepository;
use Carbon\CarbonImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name = "t_demande_autorisation")
 * @ORM\Entity(repositoryClass=DemandeRepository::class)
 */
class Demande
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
    private $corps;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $responsedBy;

    /**
     * @ORM\OneToMany(targetEntity=DocDemande::class, mappedBy="demande")
     */
    private $documents;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $currentState;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $response;

    public function __construct()
    {
        $this->documents = new ArrayCollection();
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

    public function getCorps(): ?string
    {
        return $this->corps;
    }

    public function setCorps(string $corps): self
    {
        $this->corps = $corps;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist()
     * @return $this
     */
    public function setCreatedAt(): self
    {
        $this->createdAt = CarbonImmutable::now();

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getResponsedBy(): ?User
    {
        return $this->responsedBy;
    }

    public function setResponsedBy(?User $responsedBy): self
    {
        $this->responsedBy = $responsedBy;

        return $this;
    }

    /**
     * @return Collection<int, DocDemande>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(DocDemande $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setDemande($this);
        }

        return $this;
    }

    public function removeDocument(DocDemande $document): self
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getDemande() === $this) {
                $document->setDemande(null);
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

    public function getResponse(): ?string
    {
        return $this->response;
    }

    public function setResponse(?string $response): self
    {
        $this->response = $response;

        return $this;
    }
}
