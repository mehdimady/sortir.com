<?php

namespace App\DataFixtures;

use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class VilleFixtures extends Fixture implements DependentFixtureInterface
{
    private $faker;
    public function __construct(){
        $this->faker = Faker\Factory::create('fr_FR');
    }
    public function load(ObjectManager $manager): void
    {
        for($i = 0; $i< 10; $i++){
            $ville = new Ville();
            $ville->setNom($this->faker->city());
            $ville->setCodePostal($this->faker->randomNumber(5, true));
            $manager->persist($ville);
            $this->addReference('ville-'.$i,$ville);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            EtatFixtures::class,
            CampusFixtures::class
        ];
    }
}