<?php

namespace App\DataFixtures;

use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsersFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        for($nbUsers = 1 ; $nbUsers < 30 ; $nbUsers++) {
            $user = new Users;
            $user->setEmail($faker->email);
            $user->setPassword($this->encoder->hashPassword($user, 'azerty'));
            $user->setName($faker->lastName);
            $user->setFirstname($faker->firstName);
            $user->setIsVerified($faker->numberBetween(0, 1));
            $manager->persist($user);

            // Record the category reference
            $this->addReference('user_' . $nbUsers, $user);
        }
        $manager->flush();
    }
}
