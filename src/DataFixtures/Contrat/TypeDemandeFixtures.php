<?php

namespace App\DataFixtures\Contrat;

use App\Entity\Contrat\TypeDemandeContrat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TypeDemandeFixtures extends Fixture
{
    public const TYPE_DEMANDE_REFERENCE = 'f-type-demande-contrat-';
    public function load(ObjectManager $manager): void
    {
        $typeDemandeTab = [
            [
                "lib" => "Elaboration",
                "color" => "primary"
            ],
            [
                "lib" => "Modification",
                "color" => "info"
            ],
            [
                "lib" => "Renouvellement",
                "color" => "secondary"
            ],
        ];

        $i = 0;
        foreach ($typeDemandeTab as $val){
            $typeDemande = (new TypeDemandeContrat())
                ->setLib($val["lib"])
                ->setColor($val["color"]);
            $manager->persist($typeDemande);
            $manager->flush();
            $this->addReference(self::TYPE_DEMANDE_REFERENCE.$i, $typeDemande);
            $i++;
        }

        $manager->flush();
    }
}
