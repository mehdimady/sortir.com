<?php

namespace App\services;

use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;

class GestionDesEtats
{
    public function UpdateStatesOfEvents(SortieRepository $sortieRepository, EtatRepository $etatRepository, EntityManagerInterface $manager):void
    {
//Retrieve database's Datas
        $date = new \DateTime('now');
        $sorties = $sortieRepository->findAll();
        $etats = $etatRepository->findAll();
//Check All Status of Sorties
        foreach ($sorties as $sortie) {
            switch ($sortie->getEtat()->getLibelle()) {
//En Cours
                case $etats[0] :
                    if ($sortie->getDateHeureDebut() < $date) {
                        $sortie->setEtat($etats[3]);
                        $sortie->setMotif("Délais Activité Démarrée ou Terminée - La date maximun de publication est dépassée.");
                    }
                    break;
//Ouvert
                case $etats[1] :
                    if($date > $sortie->getDateLimiteInscription() && $date < $sortie->getDateHeureDebut() || $sortie->getNbInscriptionsMax() == count($sortie->getParticipants())) {
                        $sortie->setEtat($etats[2]);
                    }
                    if ($date > $sortie->getDateLimiteInscription() && count($sortie->getParticipants()) == 0) {
                        $sortie->setEtat($etats[3]);
                        $sortie->setMotif("Délais inscription dépassé - La sortie est annulée car elle ne possède aucun participant");
                    }
                    break;
//Fermé
                case $etats[2] :
                    if ($date > $sortie->getDateHeureDebut() && $date < $sortie->getDateHeureFin()) {
                        $sortie->setEtat($etats[4]);
                    } elseif ($date > $sortie->getDateHeureFin()) {
                        $sortie->setEtat($etats[5]);
                    } elseif ($date > $sortie->getDateLimiteInscription() && count($sortie->getParticipants()) == 0) {
                        $sortie->setEtat($etats[3]);
                        $sortie->setMotif("Délais inscription dépassé - La sortie est annulée car elle ne possède aucun participant");
                    } else {
                        $sortie->setEtat($etats[2]);
                    }
                    break;
//En Cours
                case  $etats[4] :
                    if ($date > $sortie->getDateHeureFin()) {
                        $sortie->setEtat($etats[5]);
                    }
                    break;
            }
// Tous ( Check Archivage )
            if ($this->archivage($sortie)){
                $sortie->setEtat($etats[6]);
            }
            $manager->persist($sortie);
        }
        $manager->flush();
    }

    public function archivage($sortie):bool{
        $moisEnMinutes = 43200;
        return (new \DateTime('now') > $sortie->getDateHeureFin()->add(new DateInterval('PT' .$moisEnMinutes. 'M')));
    }
}