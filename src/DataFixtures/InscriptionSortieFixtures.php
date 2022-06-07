<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class InscriptionSortieFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for($i=0; $i < 42; $i++){//for sorties
            $sortie = $this->getReference('sortie-'.$i);
            $nombreInscrit = rand(2,4);
            for ($j = 0; $j <= $nombreInscrit; $j++) {
                $participant = $this->getReference('participant-'.rand(1,14));
                if($sortie->getOrganisateur()->getId() != $participant->getId()){
                    $sortie->addParticipant($participant);
                    $manager->persist($sortie);
                }
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            EtatFixtures::class,
            CampusFixtures::class,
            VilleFixtures::class,
            LieuFixtures::class,
            ParticipantFixtures::class,
            SortieFixtures::class
        ];
    }
}