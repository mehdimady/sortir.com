<?php

namespace App\DataFixtures;

use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        $villes=[];

    for($i = 0; $i< 10; $i++){
        $ville = new Ville();
        $ville->setNom($faker->city());
        $ville->setCodePostal($faker->randomNumber(5, true));
        $manager->persist($ville);
        $villes[]=$ville;
    }





        $manager->flush();
    }
}
