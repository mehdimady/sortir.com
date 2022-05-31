<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $hasher;
    public function __construct(UserPasswordHasherInterface $hasher){
        $this->hasher = $hasher;
    }
    public function load(ObjectManager $manager): void
    {

        $faker = Faker\Factory::create('fr_FR');

        $campusx = [];
        $villes = [];
        $lieux = [];
        $sorties = [];
       $etats = [];



//       fixtures CAMPUS
        foreach(["Nantes","Rennes","Niort"] as $city){
            $campus = new Campus();
            $campus->setNom($city);
            $manager->persist($campus);
            $campusx[]=$campus;
        }

//        fixtures PARTICIPANT

        $partAdmin = new Participant();
        $partAdmin->setNom($faker->lastName);
        $partAdmin->setPrenom($faker->firstName);
        $partAdmin->setTelephone("0607060706");
        $partAdmin->setEmail("admin@test.eni");
        $passAdmin = $this->hasher->hashPassword($partAdmin,'azerty');
        $partAdmin->setPassword($passAdmin);
        $partAdmin->setAdministrateur(1);
        $partAdmin->setRoles(['ROLE_USER','ROLE_ADMIN']);
        $partAdmin->setActif(1);
        $partAdmin->setCampus($campusx[0]);
        $manager->persist($partAdmin);

        $partUser = new Participant();
        $partUser->setNom($faker->lastName);
        $partUser->setPrenom($faker->firstName);
        $partUser->setTelephone("0607060706");
        $partUser->setEmail("User@test.eni");
        $passUser = $this->hasher->hashPassword($partUser,'azerty');
        $partUser->setPassword($passUser);
        $partUser->setAdministrateur(0);
        $partUser->setRoles(['ROLE_USER']);
        $partUser->setActif(1);
        $partUser->setCampus($campusx[rand(0,2)]);
        $manager->persist($partUser);

        for($i=0;$i<30;$i++){
            $part= new Participant();
            $part->setNom($faker->lastName);
            $part->setPrenom($faker->firstName);
            $tel = $faker->randomNumber(9, true);
            $part->setTelephone("0".$tel);
            $part->setEmail($faker->email());
            $pass = $this->hasher->hashPassword($part,'azerty');
            $part->setPassword($pass);
            $part->setAdministrateur(0);
            $part->setRoles(['ROLE_USER']);
            $part->setActif(1);
            $part->setCampus($campusx[rand(0,2)]);
            $manager->persist($part);
        }


//        fixtures VILLES
        for($i = 0; $i< 10; $i++){
            $ville = new Ville();
            $ville->setNom($faker->city());
            $ville->setCodePostal($faker->randomNumber(5, true));
            $manager->persist($ville);
            $villes[]=$ville;
        }
//        fixtures LIEUX
        for ($i = 0 ; $i < 50 ; $i++){
           $lieu = new Lieu();
           $lieu->setNom($faker->sentence(1));
           $lieu->setRue($faker->streetAddress());
           $lieu->setVille($villes[rand(0,9)]);
           $lieu->setLatitude($faker->latitude($min = -90, $max = 90));
           $lieu->setLongitude($faker->longitude($min = -180, $max = 180));
            $manager->persist($lieu);
            $lieux[] = $lieu;
        }
//        fixtures ETATS
        $states = ['Créée','Ouverte','Clôturée','Activité en cours','passée','Annulée'];
        foreach ($states as $state){
            $etat = new Etat();
            $etat->setLibelle($state);
            $manager->persist($etat);
            $etats[]=$etat;
        }

//        fixtures SORTIES

    for($i = 0; $i <= 5; $i++ ){
        $sortie = new Sortie();
        $sortie->setNom($faker->sentence(1));
        $sortie->setDuree(rand(30,180));
        $sortie->setNbInscriptionsMax(rand(50,150));
        $sortie->setInfosSortie($faker->paragraph(2));
        $sortie->setLieux($lieux[rand(0,49)]);

        $date = new \DateTime();
        $dateStart = '';
        $dateInterval = '';
        switch ($i){
//            creee
            case 0 :
                $date->modify('+6 week');
                $dateStart .= '+4 weeks';
                $dateInterval .= '+5 weeks';
                $sortie->setEtat($etats[0]);
                break;
//                ouverte
            case 1 :
                $date->modify('+3 week');
                $dateStart .= '+1 week';
                $dateInterval .= '+2 weeks';
                $sortie->setEtat($etats[1]);
                break;
//                cloturee
            case 2 :
                $date->modify('+1 week');
                $dateStart .= '-2 weeks';
                $dateInterval .= '-1 week';
                $sortie->setEtat($etats[2]);
                break;
//                en cours
            case 3 :
                $dateStart .= '-3 weeks';
                $dateInterval .= '-1 week';
                $sortie->setEtat($etats[3]);
                break;
//                passee
            case 4 :
                $date->modify('-1 week');
                $dateStart .= '-3 weeks';
                $dateInterval .= '-2 week';
                $sortie->setEtat($etats[4]);
                break;
//                annulee
            case 5 :
            $date->modify('-2 week');
            $dateStart .= '-4 weeks';
            $dateInterval .= '-3 week';
                $sortie->setEtat($etats[5]);
            break;
        }
        $sortie->setDateHeureDebut($date);
        $sortie->setDateLimiteInscription($faker->dateTimeInInterval($dateStart, $dateInterval));

        $manager->persist($sortie);
    }
        $manager->flush();
    }
}
