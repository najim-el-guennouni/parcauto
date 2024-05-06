<?php

namespace App\DataFixtures;

use App\Entity\Car;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
    private $passwordHasher;
    
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 20; $i++) {
            $product = new Car();
            $product->setDesignation('car ' . $i);
            $manager->persist($product);
        }

        for ($i = 0; $i < 2; $i++) {
            $user = new User();
            $user->setUsername('name ' . $i);
            $user->setRoles(['ROLE_USER']);
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                123456
            );
            $user->setPassword($hashedPassword);
            $manager->persist($user);
        }


        $manager->flush();
    }
}
