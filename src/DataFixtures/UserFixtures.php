<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Common\Util;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR'); // Correct case


        // My user
        $user = new User();
        $user->setEmail('smichimajed@gmail.com')
            ->setNom('Majed')
            ->setPrenom('Smichi')
            ->setPassword(
                $this->hasher->hashPassword($user, 123456)
            )
            ->setNumTele(12345678)
            ->setAdresse('ariana')
            ->setRoles(['ROLE_SUPER_ADMIN']);


        $manager->persist($user);

        // for ($i = 0; $i < 9; $i++) {
        //     $user = new User();
        //     $user->setEmail($faker->email())
        //         ->setPrenom($faker->firstName())
        //         ->setNom($faker->lastName())
        //         ->setPassword(
        //             $this->hasher->hashPassword($user, 'motDePasse')
        //         )
        //         ->setNumTele(12345678)
        //         ->setAdresse('ariana');;

        //     $manager->persist($user);
        // }

        $manager->flush();
    }
}
