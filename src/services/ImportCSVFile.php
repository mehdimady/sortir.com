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
            while (($data = fgetcsv($csv, 1000, ';')) !== FALSE) {
                $datas = explode(',',$data[0]);
//                dump($data);
//                dd($datas);
                if ($datas[0] != "id") {
//        index Campus
                    $indexCampus = intval($datas[1]);
                    foreach ($this->campus as $cmp) {
                        if ($cmp->getId() == $indexCampus) {
                            $cur_campus = $cmp;
                        }
                    }
//        ROLES
                    $role = str_replace('[', '', $datas[3]);
                    $role = str_replace(']', '', $role);
                    $role = str_replace('"', '', $role);
//          PASSWORD
//                       $pass = $this->hasher->hashPassword($datas[4]);
                    $participant = new Participant();
                    $participant->setCampus($cur_campus);
                    $participant->setEmail($datas[2]);
                    $participant->setRoles([$role]);
                    $participant->setPassword($datas[4]);
                    $participant->setNom($datas[5]);
                    $tmp = utf8_encode($datas[6]);
                    $participant->setPrenom(utf8_decode($tmp));
                    $participant->setTelephone($datas[7]);
                    $participant->setPseudo($datas[10]);
                    $participant->setImageFilename('noimage.jpg');
                    $manager->persist($participant);
                }
            }
        }
        fclose($csv);


        $manager->flush();
        unlink( './uploads/image/participant.csv');
    }
}