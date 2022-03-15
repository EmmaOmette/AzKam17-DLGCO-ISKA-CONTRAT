<?php

namespace App\Service;

use App\Entity\Departement;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class SetManager
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function run(User $user, Departement $departement): bool
    {
        if($departement->getMembers()->contains($user)){

            foreach ($departement->getMembers() as $userTemp){
                $userTemp
                    ->setIsManager(
                        $user->getId() === $userTemp->getId()
                    )
                    ->setRoles(
                        array_merge(
                            $userTemp->getRoles(),
                            ($departement->getSlug() === 'direction_juridique') ? ['USER_BOSS'] : []
                        )
                    );
            }
            return true;
        }
        return false;
    }
}