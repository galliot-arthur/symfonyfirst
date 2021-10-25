<?php

namespace App\DataFixtures;

use App\Entity\Annonces;
use App\Entity\Comments;
use App\Entity\Images;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AnnoncesFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($nbAnnonces = 1; $nbAnnonces < 109; $nbAnnonces++) {
            $user = $this->getReference('user_' . $faker->numberBetween(1, 29));
            $categorie = $this->getReference('categorie_' . $faker->numberBetween(1, 5));

            $annonce = new Annonces;
            $annonce
                ->setUsers($user)
                ->setCategories($categorie)
                ->setTitle($faker->realText(25))
                ->setContent($faker->realText(200))
                ->setActive($faker->numberBetween(0, 1))
                ->setPrice($faker->numberBetween(3, 1000));

            // Pictures part    
            for ($image = 1; $image < 3; $image++) {
                $img = $faker->image('public/uploads');
                $imageAnnonce = new Images;
                $imageAnnonce->setName(str_replace(
                    'public/uploads\\',
                    '',
                    $img)
                );
                $annonce->addImage($imageAnnonce);
            }

            $manager->persist($annonce);
            
            // Comments part
            for ($nbComment = 1; $nbComment < 6; $nbComment++) {
                $user = $this->getReference('user_' . $faker->numberBetween(1, 8));
                
                $comment = new Comments;
                $comment
                ->setAnnonces($annonce)
                ->setAuthor($user)
                ->setContent($faker->realText(150));
                
                $manager->persist($comment);
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoriesFixtures::class,
            UsersFixtures::class
        ];

    }
}
