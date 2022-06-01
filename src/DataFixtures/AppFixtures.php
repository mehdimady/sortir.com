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
    private $faker;
    public function __construct(UserPasswordHasherInterface $hasher){
        $this->hasher = $hasher;
        $this->faker = Faker\Factory::create('fr_FR');
    }
    public function load(ObjectManager $manager): void
    {
        $campus = $this->makeCampus($manager);
        $etats = $this->makeEtats($manager);
        $villes = $this->makeVilles($manager);
        $lieux = $this->makeLieux($manager,$villes);
        $sorties = $this->makeSorties($manager,$lieux,$etats);
//        USERS
        $this->makeSpecificUsers($manager,TRUE,$campus,$sorties);
        $this->makeSpecificUsers($manager,FALSE,$campus,$sorties);
        $users = $this->makeUsers($manager,$campus);

        $this->addParticipant($manager,$sorties,$users);

        $manager->flush();
    }

    public function makeVilles(ObjectManager $manager):array{
        for($i = 0; $i< 10; $i++){
            $ville = new Ville();
            $ville->setNom($this->faker->city());
            $ville->setCodePostal($this->faker->randomNumber(5, true));
            $manager->persist($ville);
            $villes[]=$ville;
        }
        return $villes;
    }

    public function makeLieux(ObjectManager $manager,array $villes):array{
        for ($i = 0 ; $i < 10 ; $i++){
            $lieu = new Lieu();
            $lieu->setNom($this->faker->sentence(1));
            $lieu->setRue($this->faker->streetAddress());
            $lieu->setVille($villes[rand(0,9)]);
            $lieu->setLatitude($this->faker->latitude($min = -90, $max = 90));
            $lieu->setLongitude($this->faker->longitude($min = -180, $max = 180));
            $manager->persist($lieu);
            $lieux[] = $lieu;
        }
        return $lieux;
    }

    public function makeEtats(ObjectManager $manager):array{
        $states = ['Créée','Ouverte','Clôturée','Activité en cours','passée','Annulée'];
        foreach ($states as $state){
            $etat = new Etat();
            $etat->setLibelle($state);
            $manager->persist($etat);
            $etats[]=$etat;
        }
        return $etats;
    }

    public function makeCampus(ObjectManager $manager):array{
        foreach(["Nantes","Rennes","Niort"] as $city){
            $campus = new Campus();
            $campus->setNom($city);
            $manager->persist($campus);
            $allCampus[]=$campus;
        }
        return $allCampus;
    }

    public function makeSorties(ObjectManager $manager, array $lieux,array $etats):array{
        for($i = 0; $i <= 5; $i++ ){
            $sortie = new Sortie();
            $sortie->setNom($this->faker->sentence(1));
            $sortie->setDuree(rand(30,180));
            $sortie->setNbInscriptionsMax(rand(10,50));
            $sortie->setInfosSortie($this->faker->paragraph(2));
            $sortie->setLieux($lieux[rand(0,9)]);

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
            $sortie->setDateLimiteInscription($this->faker->dateTimeInInterval($dateStart, $dateInterval));
            $sorties[] = $sortie;
            $manager->persist($sortie);
        }
        return $sorties;
    }

    public function makeSpecificUsers(ObjectManager $manager,bool $isAdmin,array $campus,array $sorties){
        $participant = new Participant();
        $participant->setPseudo($this->faker->lastName);
        $participant->setNom($this->faker->lastName);
        $participant->setPrenom($this->faker->firstName);
        $participant->setTelephone("0607060706");
        $participant->setPassword($this->hasher->hashPassword($participant,'azerty'));
        $participant->setActif(1);
        if($isAdmin){
            $participant->setEmail("admin@test.eni");
            $participant->setRoles(['ROLE_USER','ROLE_ADMIN']);
            $participant->setAdministrateur(1);
            $indiceSortie=0;
            $indiceCampus=0;
        }else{
            $participant->setEmail("user@test.eni");
            $participant->setRoles(['ROLE_USER']);
            $participant->setAdministrateur(0);
            $indiceSortie=3;
            $indiceCampus=1;
        }
        $participant->setCampus($campus[$indiceCampus]);
        for($i=0; $i<3; $i++){
            $currentSortie = $sorties[$i+$indiceSortie];
            $currentSortie->setCampus($campus[$indiceCampus]);
            $participant->addOrganisateur($currentSortie);
        }
        $manager->persist($participant);
    }

    public function makeUsers(ObjectManager $manager,array $campus):array{
        for($i=0;$i<30;$i++){
            $part= new Participant();
            $part->setPseudo($this->faker->lastName);
            $part->setNom($this->faker->lastName);
            $part->setPrenom($this->faker->firstName);
            $tel = $this->faker->randomNumber(9, true);
            $part->setTelephone("0".$tel);
            $part->setEmail($this->faker->email());
            $pass = $this->hasher->hashPassword($part,'azerty');
            $part->setPassword($pass);
            $part->setAdministrateur(0);
            $part->setRoles(['ROLE_USER']);
            $part->setActif(1);
            $part->setCampus($campus[rand(0,2)]);
            $manager->persist($part);
            $users[] = $part;
        }
        return $users;
    }

    private function addParticipant(ObjectManager $manager, array $sorties, array $users)
    {
        for($i=0;$i<=5;$i++){
            if($i!=0) {
                $hasard = rand(10, 30);
                for ($j = 0; $j < $hasard; $j++) {
                    $sorties[$i]->addParticipant($users[rand(0, 29)]);
                    $manager->persist($sorties[$i]);
                }
            }
        }
    }
}