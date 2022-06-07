<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CampusFixtures extends Fixture implements DependentFixtureInterface
{
    private array $campus =  ["Nantes","Rennes","Niort"] ;
    public function load(ObjectManager $manager): void
    {
        for($i=0;$i<count($this->campus);$i++){
            $campus = new Campus();
            $campus->setNom($this->campus[$i]);
            $manager->persist($campus);
            $this->addReference('campus-'.$i,$campus);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            EtatFixtures::class
        ];
    }
}