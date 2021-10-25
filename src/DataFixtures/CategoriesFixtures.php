<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoriesFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        $categories = [
            1 => [
                'name' => 'Armoire'
            ],
            2 => [
                'name' => 'Etagère'
            ],
            3 => [
                'name' => 'Voitures'
            ],
            4 => [
                'name' => 'Outils'
            ],
            5 => [
                'name' => 'Instruments de Musique'
            ],
        ];

        foreach($categories as $key => $value) {
            $categorie = new Categories;
            $categorie->setName($value['name']);
            $manager->persist($categorie);

            $this->addReference('categorie_' . $key, $categorie);
        }
        $manager->flush();
    }
}
