<?php

namespace App\Entity\Contrat;

use App\Entity\Departement;
use App\Entity\User;
use App\Entity\UserJuridique;
use App\Repository\Contrat\ContratRepository;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass=ContratRepository::class)
 */
class Contrat
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="doctrine.uuid_generator")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TypeDemandeContrat::class, inversedBy="departementInitiateur")
     */
    private $typeDemande;

    /**
     * @ORM\ManyToOne(targetEntity=Departement::class, inversedBy="contratsInities")
     */
    private $departementInitiateur;

    /**
     * @ORM\Column(type="text")
     */
    private $objet;

    /**
     * @ORM\Column(type="text")
     */
    private $clausesParticulieres;

    /**
     * @ORM\Column(type="date")
     */
    private $entreeVigueurAt;

    /**
     * @ORM\Column(type="date")
     */
    private $finContratAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $dureeContrat;

    /**
     * @ORM\ManyToOne(targetEntity=ModeFacturation::class, inversedBy="contrats")
     */
    private $modeFacturation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $delaiDenonciation;

    /**
     * @ORM\Column(type="text")
     */
    private $periodicitePaiement;

    /**
     * @ORM\ManyToOne(targetEntity=ModeReglement::class, inversedBy="contrats")
     */
    private $modeReglement;

    /**
     * @ORM\ManyToOne(targetEntity=ModeRenouvellement::class, inversedBy="noteDerniereEvaluation")
     */
    private $modeRenouvellement;

    /**
     * @ORM\Column(type="date")
     */
    private $dateDerniereEvaluation;

    /**
     * @ORM\ManyToOne(targetEntity=IdentiteConcontractant::class, inversedBy="contrats")
     */
    private $identiteConcontractant;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $files = [];

    /**
     * En attendant de trouver une solution pour la propriété de base
     * @ORM\Column(type="string", length=255)
     */
    private $identiteConcontractantTemp;

    /**
     * @ORM\Column(type="text")
     */
    private $objetConditionModification;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $libConditionModification;

    /**
     * @ORM\OneToMany(targetEntity=DocumentContrat::class, mappedBy="contrat")
     */
    private $documentContrats;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $currentState;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="contratsInities")
     * @ORM\JoinColumn(nullable=false)
     */
    private $agentInitiateur;

    /**
     * @ORM\ManyToOne(targetEntity=UserJuridique::class, inversedBy="contrats")
     */
    private $userJuridique;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $debutAttribution;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fintAttribution;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    public function __construct()
    {
        $this->documentContrats = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getTypeDemande(): ?TypeDemandeContrat
    {
        return $this->typeDemande;
    }

    public function setTypeDemande(?TypeDemandeContrat $typeDemande): self
    {
        $this->typeDemande = $typeDemande;

        return $this;
    }

    public function getDepartementInitiateur(): ?Departement
    {
        return $this->departementInitiateur;
    }

    public function setDepartementInitiateur(?Departement $departementInitiateur): self
    {
        $this->departementInitiateur = $departementInitiateur;

        return $this;
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

    public function getClausesParticulieres(): ?string
    {
        return $this->clausesParticulieres;
    }

    public function setClausesParticulieres(string $clausesParticulieres): self
    {
        $this->clausesParticulieres = $clausesParticulieres;

        return $this;
    }

    public function getEntreeVigueurAt(): ?\DateTimeInterface
    {
        return $this->entreeVigueurAt;
    }

    public function setEntreeVigueurAt(\DateTimeInterface $entreeVigueurAt): self
    {
        $this->entreeVigueurAt = $entreeVigueurAt;

        return $this;
    }

    public function getFinContratAt(): ?\DateTimeInterface
    {
        return $this->finContratAt;
    }

    public function setFinContratAt(\DateTimeInterface $finContratAt): self
    {
        $this->finContratAt = $finContratAt;

        return $this;
    }

    public function getDureeContrat(): ?string
    {
        return $this->dureeContrat;
    }

    public function setDureeContrat(string $dureeContrat): self
    {
        $this->dureeContrat = $dureeContrat;

        return $this;
    }

    public function getModeFacturation(): ?ModeFacturation
    {
        return $this->modeFacturation;
    }

    public function setModeFacturation(?ModeFacturation $modeFacturation): self
    {
        $this->modeFacturation = $modeFacturation;

        return $this;
    }

    public function getDelaiDenonciation(): ?string
    {
        return $this->delaiDenonciation;
    }

    public function setDelaiDenonciation(string $delaiDenonciation): self
    {
        $this->delaiDenonciation = $delaiDenonciation;

        return $this;
    }

    public function getPeriodicitePaiement(): ?string
    {
        return $this->periodicitePaiement;
    }

    public function setPeriodicitePaiement(string $periodicitePaiement): self
    {
        $this->periodicitePaiement = $periodicitePaiement;

        return $this;
    }

    public function getModeReglement(): ?ModeReglement
    {
        return $this->modeReglement;
    }

    public function setModeReglement(?ModeReglement $modeReglement): self
    {
        $this->modeReglement = $modeReglement;

        return $this;
    }

    public function getModeRenouvellement(): ?ModeRenouvellement
    {
        return $this->modeRenouvellement;
    }

    public function setModeRenouvellement(?ModeRenouvellement $modeRenouvellement): self
    {
        $this->modeRenouvellement = $modeRenouvellement;

        return $this;
    }

    public function getDateDerniereEvaluation(): ?\DateTimeInterface
    {
        return $this->dateDerniereEvaluation;
    }

    public function setDateDerniereEvaluation(\DateTimeInterface $dateDerniereEvaluation): self
    {
        $this->dateDerniereEvaluation = $dateDerniereEvaluation;

        return $this;
    }

    public function getIdentiteConcontractant(): ?IdentiteConcontractant
    {
        return $this->identiteConcontractant;
    }

    public function setIdentiteConcontractant(?IdentiteConcontractant $identiteConcontractant): self
    {
        $this->identiteConcontractant = $identiteConcontractant;

        return $this;
    }

    public function getFiles(): ?array
    {
        return $this->files;
    }

    public function setFiles(?array $files): self
    {
        $this->files = $files;

        return $this;
    }

    public function getIdentiteConcontractantTemp(): ?string
    {
        return $this->identiteConcontractantTemp;
    }

    public function setIdentiteConcontractantTemp(string $identiteConcontractantTemp): self
    {
        $this->identiteConcontractantTemp = $identiteConcontractantTemp;

        return $this;
    }

    public function getObjetConditionModification(): ?string
    {
        return $this->objetConditionModification;
    }

    public function setObjetConditionModification(string $objetConditionModification): self
    {
        $this->objetConditionModification = $objetConditionModification;

        return $this;
    }

    public function getLibConditionModification(): ?string
    {
        return $this->libConditionModification;
    }

    public function setLibConditionModification(?string $libConditionModification): self
    {
        $this->libConditionModification = $libConditionModification;

        return $this;
    }

    /**
     * @return Collection<int, DocumentContrat>
     */
    public function getDocumentContrats(): Collection
    {
        return $this->documentContrats;
    }

    public function addDocumentContrat(DocumentContrat $documentContrat): self
    {
        if (!$this->documentContrats->contains($documentContrat)) {
            $this->documentContrats[] = $documentContrat;
            $documentContrat->setContrat($this);
        }

        return $this;
    }

    public function removeDocumentContrat(DocumentContrat $documentContrat): self
    {
        if ($this->documentContrats->removeElement($documentContrat)) {
            // set the owning side to null (unless already changed)
            if ($documentContrat->getContrat() === $this) {
                $documentContrat->setContrat(null);
            }
        }

        return $this;
    }

    public function getCurrentState(): ?string
    {
        return $this->currentState;
    }

    public function setCurrentState(?string $currentState): self
    {
        $this->currentState = $currentState;

        return $this;
    }

    public function getAgentInitiateur(): ?User
    {
        return $this->agentInitiateur;
    }

    public function setAgentInitiateur(?User $agentInitiateur): self
    {
        $this->agentInitiateur = $agentInitiateur;

        return $this;
    }

    public function getUserJuridique(): ?UserJuridique
    {
        return $this->userJuridique;
    }

    public function setUserJuridique(?UserJuridique $userJuridique): self
    {
        $this->userJuridique = $userJuridique;

        return $this;
    }

    public function getDebutAttribution(): ?\DateTimeInterface
    {
        return $this->debutAttribution;
    }

    public function setDebutAttribution(?\DateTimeInterface $debutAttribution): self
    {
        $this->debutAttribution = $debutAttribution;

        return $this;
    }

    public function getFintAttribution(): ?\DateTimeInterface
    {
        return $this->fintAttribution;
    }

    public function setFintAttribution(?\DateTimeInterface $fintAttribution): self
    {
        $this->fintAttribution = $fintAttribution;

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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     * @return $this
     */
    public function setUpdatedAt(): self
    {
        $this->updatedAt = Carbon::now();

        return $this;
    }
}
