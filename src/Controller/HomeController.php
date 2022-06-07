<?php

namespace App\Controller;

use App\services\GestionDesEtats;
use App\services\SecurityControl;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
#[IsGranted('ROLE_USER')]
#[Route('/accueil', name: 'app_')]
class HomeController extends AbstractController
{
    private $security;
    public function __construct(Security $security){
        $this->security = $security;
//        if ($this->security->isGranted('ROLE_ADMIN')) {
//        }
    }

    #[Route('/', name: 'home')]
    public function displayAllEvents(SortieRepository $sortieRepository,EtatRepository $etatRepository, GestionDesEtats $gestionDesEtats,EntityManagerInterface $manager,SecurityControl $control): Response
    {
        if($control->userIsActive($this->getUser())){

            $gestionDesEtats->UpdateStatesOfEvents($sortieRepository,$etatRepository,$manager);

            $sorties = $sortieRepository->findAll();

            if($this->getUser() != null){
                foreach ($sorties as $sortie){
                    $inscrit = false;
                    foreach ($sortie->getParticipants() as $participant) {
                        if ($this->getUser()->getUserIdentifier() == $participant->getEmail()) {
                            $inscrit = true;
                        }
                    }
                    $sortiesReturn[] = ['sortie' => $sortie, 'inscrit' => $inscrit];
                }
            }else{
                $this->addFlash('error','Veuillez vous connecter ou vous inscrire !');
                return $this->redirectToRoute('app_login');
            }

            return $this->render('home/home.html.twig', [
                'title' => 'Campus | sorties',
                "inscrit"=>$inscrit,
                'sorties'=>$sortiesReturn
            ]);
        }else{
            $this->addFlash('error',"Votre compte a été déactivé! Veuillez contacter l'administrateur.");
            return $this->redirectToRoute('app_login');
        }
    }
}
