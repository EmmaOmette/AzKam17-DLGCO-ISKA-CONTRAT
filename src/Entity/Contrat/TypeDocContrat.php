<?php

namespace App\Entity\Contrat;

use App\Entity\Abstracts\Document;
use App\Repository\Contrat\TypeDocContratRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table (name="t_contrat_type_doc_contrat")
 * @ORM\Entity(repositoryClass=TypeDocContratRepository::class)
 */
class TypeDocContrat extends Document
{
}
