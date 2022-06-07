<?php

namespace App\DataFixtures;

use App\Entity\Participant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ParticipantFixtures extends Fixture implements DependentFixtureInterface
{
    private $hasher;
    private $faker;

    public function __construct(UserPasswordHasherInterface $hasher){
        $this->hasher = $hasher;
        $this->faker = Faker\Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        for($i=0;$i<15;$i++){
            $participant= new Participant();
            $participant->setPseudo($this->faker->word().rand(1,99));
            $participant->setNom($this->faker->lastName);
            $participant->setPrenom($this->faker->firstName);
            $tel = $this->faker->randomNumber(9, true);
            $participant->setTelephone("0".$tel);
            $pass = $this->hasher->hashPassword($participant,'azerty');
            $participant->setPassword($pass);
    //      ajout  "admin@test.eni" et "user@test.eni"
            if($i==0){
                $participant->setEmail("admin@test.eni");
            }elseif ($i==1){
                $participant->setEmail("user@test.eni");
            }else{
                $participant->setEmail($this->faker->email());
            }
            if($i==0){
                $participant->setRoles(['ROLE_ADMIN']);
                $participant->setAdministrateur(1);
            }else{
                $participant->setAdministrateur(0);
                $participant->setRoles(['ROLE_USER']);
            }
            $participant->setImageFilename('noimage.jpg');
            $participant->setActif(1);
            $participant->setCampus($this->getReference('campus-'.rand(0,2)));
            $manager->persist($participant);
            $this->addReference('participant-'.$i,$participant);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            EtatFixtures::class,
            CampusFixtures::class,
            VilleFixtures::class,
            LieuFixtures::class
        ];
    }
}