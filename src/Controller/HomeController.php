<?php

namespace App\Controller;

use App\Etats\GestionDesEtats;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/', name: 'app_')]
class HomeController extends AbstractController
{
    #[Route('', name: 'home')]
    public function displaySortiesOfCampus(SortieRepository $sortieRepository,EtatRepository $etatRepository, GestionDesEtats $gestionDesEtats,EntityManagerInterface $manager): Response
    {
        $gestionDesEtats->UpdateStatesOfEvents($sortieRepository,$etatRepository,$manager);
        $sortiesReturn=[];
        if($this->getUser() != null){
            $user_current = $this->getUser()->getCampus();
            $sorties= $sortieRepository->findBy(["campus"=>$user_current]);
            foreach ($sorties as $sortie){
                $inscrit = false;
                foreach ($sortie->getParticipants() as $participant){
                    if( $this->getUser()->getUserIdentifier() == $participant->getEmail()) {
                        $inscrit = true;
                    }
                }
                $sortiesReturn[] = ['sortie' => $sortie, 'inscrit' => $inscrit];
            }
        }
        else{
            return $this->redirectToRoute('app_login');
        }

        return $this->render('home/home.html.twig', [
            'title' => 'Campus | sorties',
            'sorties'=>$sortiesReturn
        ]);
    }
}
