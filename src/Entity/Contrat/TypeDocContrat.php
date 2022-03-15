<?php

namespace App\Entity\Contrat;

use App\Entity\Abstracts\Document;
use App\Repository\Contrat\TypeDocContratRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TypeDocContratRepository::class)
 */
class TypeDocContrat extends Document
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    public function getId(): ?int
    {
        return $this->id;
    }
}
