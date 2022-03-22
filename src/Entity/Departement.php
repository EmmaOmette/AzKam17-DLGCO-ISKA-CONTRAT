<?php

namespace App\Entity;

use App\Entity\Contrat\Contrat;
use App\Repository\DepartementRepository;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table (name="t_departement")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass=DepartementRepository::class)
 */
class Departement
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
     * @ORM\Column(type="string", length=255)
     */
    private $color;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="departement")
     */
    private $members;

    /**
     * @ORM\OneToMany(targetEntity=Contrat::class, mappedBy="departementInitiateur")
     */
    private $contratsInities;

    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->contratsInities = new ArrayCollection();
    }

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @ORM\PrePersist()
     * @return $this
     */
    public function setSlug(): self
    {
        $this->slug = (new Slugify())->slugify(
            $this->getLib(), '_'
        );

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(User $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
            $member->setDepartement($this);
        }

        return $this;
    }

    public function removeMember(User $member): self
    {
        if ($this->members->removeElement($member)) {
            // set the owning side to null (unless already changed)
            if ($member->getDepartement() === $this) {
                $member->setDepartement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Contrat>
     */
    public function getContratsInities(): Collection
    {
        return $this->contratsInities;
    }

    public function addContratsInity(Contrat $contratsInity): self
    {
        if (!$this->contratsInities->contains($contratsInity)) {
            $this->contratsInities[] = $contratsInity;
            $contratsInity->setDepartementInitiateur($this);
        }

        return $this;
    }

    public function removeContratsInity(Contrat $contratsInity): self
    {
        if ($this->contratsInities->removeElement($contratsInity)) {
            // set the owning side to null (unless already changed)
            if ($contratsInity->getDepartementInitiateur() === $this) {
                $contratsInity->setDepartementInitiateur(null);
            }
        }

        return $this;
    }

}
