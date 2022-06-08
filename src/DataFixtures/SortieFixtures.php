<?php

namespace App\DataFixtures;

use App\Entity\Sortie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class SortieFixtures extends Fixture implements  DependentFixtureInterface
{
    private $faker;

    public function __construct(){
        $this->faker = Faker\Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        $indexSortie = 0;
        for($j=0;$j<=6;$j++){//for states
            for($i = 0; $i <= 5; $i++ ) {
                $dates= $this->setDateSortie($j);
                $sortie = new Sortie();
                $sortie->setNom(($this->getReference('etat-'.$j))->getLibelle().' - '.$this->faker->word.' - '.$i);
                $sortie->setDuree(rand(30, 180));
                $sortie->setNbInscriptionsMax(rand(5, 10));
                $sortie->setInfosSortie($this->faker->paragraph(2));
                $sortie->setLieux($this->getReference('lieu-'.rand(0, 9)));
                $sortie->setEtat($this->getReference('etat-'.$j));
                $sortie->setDateHeureDebut((new \DateTime('now'))->modify($dates[0].' days +'.rand(1,11).' Hours +'.rand(1,59).' Minutes'));
                $sortie->setDateLimiteInscription((new \DateTime('now'))->modify($dates[1].' days'));
                $idOrganisateur = rand(0,14);
                $sortie->setOrganisateur($this->getReference('participant-'.$idOrganisateur));
                $sortie->setCampus(($this->getReference('participant-'.$idOrganisateur))->getCampus());
                $dateDebut = clone ($sortie->getDateHeureDebut());
                $sortie->setDateHeureFin($dateDebut->modify("+{$sortie->getDuree()} minutes"));
                $manager->persist($sortie);
                $this->addReference('sortie-'.$indexSortie,$sortie);
                $indexSortie++;
            }
        }
        $manager->flush();
    }

    public function setDateSortie(int $index){
//     index reference
//     0:'En création', 1:'Ouvert', 2:'Fermé', 3:'Annulé', 4:'En cours', 5:'Terminé', 6:'Historisé'
        switch ($index){
            case 0 :
                return ['+'.rand(210,360),'+'.rand(60,180)];
            case 1 :
                return ['+'.rand(150,250),'+'.rand(60,120)];
            case 2 :
                return ['+'.rand(120,150),'-'.rand(30,60)];
            case 3 :
                return ['+'.rand(30,60),'-'.rand(60,120)];
            case 4 :
                return ['-1','-'.rand(30,60)];
            case 5 :
                return ['-'.rand(5,10),'-'.rand(30,60)];
            case 6 :
                return ['-'.rand(60,250),'-'.rand(30,59)];
            default:
                break;
        }
    }

    public function getDependencies()
    {
        return [
            EtatFixtures::class,
            CampusFixtures::class,
            VilleFixtures::class,
            LieuFixtures::class,
            ParticipantFixtures::class
        ];
    }
}