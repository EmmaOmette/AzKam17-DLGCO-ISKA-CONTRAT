<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\UserJuridique;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var \Faker\Generator
     */
    private $faker;
    /**
     * @var UserPasswordHasherInterface
     */
    private $hasher;

    public function __construct(UserPasswordHasherInterface  $hasher)
    {
        $this->faker = \Faker\Factory::create();
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $departements = [];
        for($i = 0; $i<6; $i++){
            $departements[] = $this->getReference('departement-fixtures-'.$i);
        }

        $j = 0;
        foreach ($departements as $departement){
            for($i = 0; $i < 10; $i++){
                $u = $this->faker->firstName;
                $l = $this->faker->lastName;
                $user = (new User())
                    ->setFirstName($u)
                    ->setLastName($l)
                    ->setDepartement($departement)
                    ->setEmail($this->faker->email)
                    ->setIsManager( $i == 4 );

                $roles = [];
                if($i == 4){
                    if($departement->getSlug() === 'direction_juridique'){
                        $roles[] = 'ROLE_USER_BOSS_JURIDIQUE';
                        $userJuridique = (new UserJuridique())
                            ->setUser($user)
                            ->setTempsTraitement(mt_rand(2, 15))
                            ->setNbrDemandesRefusees(0)
                            ->setNbrDemandesValidees(0)
                        ;
                        $manager->persist($userJuridique);
                    }else{
                        $roles[] = 'ROLE_USER_MANAGER';
                    }
                }else if($departement->getSlug() === 'direction_juridique'){
                    $roles[] = 'ROLE_JURIDIQUE';
                    $userJuridique = (new UserJuridique())
                        ->setUser($user)
                        ->setTempsTraitement(mt_rand(2, 15))
                        ->setNbrDemandesRefusees(0)
                        ->setNbrDemandesValidees(0)
                    ;
                    $manager->persist($userJuridique);
                }

                $user->setRoles($roles);

                $user->setPassword(
                    $this->hasher->hashPassword(
                        $user, 'azerty'
                    )
                );

                $manager->persist($user);
                $manager->flush();
                $this->setReference('user-fixtures-'.$i.$j, $user);
            }
            $j++;
        }

    }

    public function getDependencies(): array
    {
        return [
          DepartementFixtures::class
        ];
    }
}
