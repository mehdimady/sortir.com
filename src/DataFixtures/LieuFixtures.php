<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class LieuFixtures extends Fixture implements DependentFixtureInterface
{
    private $faker;

    public function __construct(){
        $this->faker = Faker\Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0 ; $i < 10 ; $i++){
            $lieu = new Lieu();
            $lieu->setNom($this->faker->sentence(1));
            $lieu->setRue($this->faker->streetAddress());
            $lieu->setVille($this->getReference('ville-'.rand(0,9)));
            $lieu->setLatitude($this->faker->latitude($min = -90, $max = 90));
            $lieu->setLongitude($this->faker->longitude($min = -180, $max = 180));
            $manager->persist($lieu);
            $this->addReference('lieu-'.$i,$lieu);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            EtatFixtures::class,
            CampusFixtures::class,
            VilleFixtures::class
        ];
    }
}