<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EtatFixtures extends Fixture
{
    private array $states =  ['En création','Ouvert','Fermé','Annulé','En cours','Terminé','Historisé'];
    public function load(ObjectManager $manager): void
    {
        for($i=0;$i<=6;$i++){
            $etat = new Etat();
            $etat->setLibelle($this->states[$i]);
            $manager->persist($etat);
            $this->addReference('etat-'.$i,$etat);
        }
        $manager->flush();
    }
}