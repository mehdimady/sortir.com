<?php

namespace App\services;

use App\Entity\Participant;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ImportCSVFile
{
    private $hasher;
    private $campus;

    public function __construct(UserPasswordHasherInterface $hasher,CampusRepository $campusRepository){
        $this->hasher = $hasher;
        $this->campus= $campusRepository->findAll();
    }

    public function test(EntityManagerInterface $manager)
    {
        if (($csv = fopen("./uploads/image/participant.csv", "r")) !== false) {
            while (($data = fgetcsv($csv, 1000, ";")) !== FALSE) {
                if ($data[0] != "id") {
//        index Campus
                    $indexCampus = intval($data[1]);
                    foreach ($this->campus as $cmp) {
                        if ($cmp->getId() == $indexCampus) {
                            $cur_campus = $cmp;
                        }
                    }
//        ROLES
                    $role = str_replace('[', '', $data[3]);
                    $role = str_replace(']', '', $role);
                    $role = str_replace('"', '', $role);
//          PASSWORD
//                       $pass = $this->hasher->hashPassword();
                    $participant = new Participant();
                    $participant->setCampus($cur_campus);
                    $participant->setEmail($data[2]);
                    $participant->setRoles([$role]);
                    $participant->setPassword($data[4]);
                    $participant->setNom($data[5]);
                    $participant->setPrenom($data[6]);
                    $participant->setTelephone($data[7]);
                    $participant->setPseudo($data[10]);
                    $participant->setImageFilename('noimage.jpg');
                    $manager->persist($participant);
                }
            }
        }
        fclose($csv);
        $manager->flush();
        unlink( './uploads/image/participant.csv');
    }




    public function encodeBinary($data):string{
        $binary = pack('H*', base_convert($data, 2, 16));
        return preg_replace('/[[:^print:]]/', $binary, $data);
    }
}