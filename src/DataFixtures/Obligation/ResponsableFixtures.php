<?php

namespace App\DataFixtures\Obligation;

use App\Entity\Obligation\Responsable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ResponsableFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $obl = [
            "Sous-Direction Marketing",
            "Sous-Direction Communication",
            "Direction des affaires administratives",
            "Direction Juridique et règlementaire",
            "Sous-Direction clientèle",
            "Direction technique",
            "Direction commerciale",
            "Division Roaming-interconnexion",
            "Direction vente entreprise",
            "Direction vente grand public",
            "Direction du système d'information",
            "Direction des ressources humaines",
        ];

        foreach ($obl as $o){
            $obli = (new Responsable())
                ->setLib($o)
                ->setDispoDeroulante(true)
            ;

            $manager->persist($obli);
            $manager->flush();
        }

    }
}
