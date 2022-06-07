<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Form\AnnuleType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\services\GestionDesEtats;
use App\services\SecurityControl;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Sortie;
use App\Form\SortieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/sortie', name: 'sortie_')]
class SortieController extends AbstractController
{
    /* Liste des index des  états =  0:'En création',1: 'Ouvert',2: 'Fermé',3: 'Annulé',4: 'En cours',5: 'Terminé',6: 'Historisé' */
    private $etats;
    private $security;

    public function __construct(EtatRepository $repo, Security $security){
        $this->etats = $repo->findAll();
        $this->security = $security;
    }

    #[Route('/', name: 'home')]
    public function displayAllEvents(SortieRepository $sortieRepository,EtatRepository $etatRepository, GestionDesEtats $gestionDesEtats,EntityManagerInterface $manager,SecurityControl $control): Response
    {
        if ($control->userIsActive($this->getUser())) {

            $gestionDesEtats->UpdateStatesOfEvents($sortieRepository, $etatRepository, $manager);

            $sorties = $sortieRepository->findAll();

            if ($this->getUser() != null) {
                foreach ($sorties as $sortie) {
                    $inscrit = false;
                    foreach ($sortie->getParticipants() as $participant) {
                        if ($this->getUser()->getUserIdentifier() == $participant->getEmail()) {
                            $inscrit = true;
                        }
                    }
                    $sortiesReturn[] = ['sortie' => $sortie, 'inscrit' => $inscrit];
                }
            } else {
                $this->addFlash('error', 'Veuillez vous connecter ou vous inscrire !');
                return $this->redirectToRoute('app_login');
            }

            return $this->render('sortie/home.html.twig', [
                'title' => 'Campus | sorties',
                "inscrit" => $inscrit,
                'sorties' => $sortiesReturn
            ]);
        } else {
            $this->addFlash('error', "Votre compte a été déactivé! Veuillez contacter l'administrateur.");
            return $this->redirectToRoute('app_login');
        }
    }

    #[Route('/{id}', name: 'affiche',requirements: ['id' => '\d+'])]
    public function displayOne(SortieRepository $sortieRepository,int $id): Response    {
        $sortie = $sortieRepository->find($id);
        $listeParticipant = $sortie->getParticipants();
        if($sortie->getEtat()->getLibelle() == $this->etats[3]){
            $motif = $sortie->getMotif();
            return $this->render('sortie/affiche.html.twig', [
                'title' => "Afficher une sortie",
                "sortie" =>$sortie,
                "motif"=>$motif,
                "listeParticipants"=>$listeParticipant
            ]);
        }else{
            return $this->render('sortie/affiche.html.twig', [
                'title' => "Afficher une sortie",
                "sortie" =>$sortie,
                "listeParticipants"=>$listeParticipant
            ]);
        }
    }

    #[Route('/create', name: 'create')]
    public function createSortie(Request $request, EtatRepository $etatRepository,EntityManagerInterface $em ): Response
    {
        $etats = $etatRepository->findAll();
        $user =$this->getUser();
        $sortie =new Sortie();
        $sortie->setOrganisateur($user);
        $sortie->setCampus($user->getCampus());
        $sortie->setEtat($etats[0]);
        $sortieForm = $this->createForm(SortieType::class,$sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() and $sortieForm->isValid() ){
            $em->persist($sortie);
            $em->flush();
            $this->addFlash('success','La sortie a bien été créée !');
            return $this->redirectToRoute('sortie_home');
        }

        return $this->render('sortie/index.html.twig', [
            'title' => 'Créer une sortie',
            'sortieForm' => $sortieForm->createView(),
        ]);
    }

    #[Route('/inscrire/{id}', name: 'inscrire',requirements: ['id' => '\d+'])]
    public function registerSortie(int $id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager )
    {
        $user = $this->getUser();
        if ($user!=null){
            $sortie = $sortieRepository->find($id);
            $maxInscrit = $sortie->getNbInscriptionsMax();
            $nbInscrit = count($sortie->getParticipants());
            $sortie->addParticipant($user);
            if ( $nbInscrit == $maxInscrit){
                $sortie->setEtat($this->etats[3]);
            }
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success','Vous êtes inscrit !');
            return $this->redirectToRoute('sortie_home');
        }
        else{
            $this->addFlash('warning','Veuillez vous connecter !');
            return $this->redirectToRoute('sortie_home');
        }
    }

    #[Route('/desister/{id}', name: 'desister',requirements: ['id' => '\d+'])]
    public function removeSortie(int $id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager )
    {
        $user = $this->getUser();
        if ($user!=null){
            $sortie = $sortieRepository->find($id);
            $sortie->removeParticipant($user);
            $dateFin = $sortie->getDateLimiteInscription();
            if (new \DateTime('now') < $dateFin){
                $sortie->setEtat($this->etats[1]);
            }
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success','Vous êtes désinscrit !');
            return $this->redirectToRoute('sortie_home');
        }
        else{
            $this->addFlash('warning','Veuillez vous connecter !');
            return $this->redirectToRoute('sortie_home');
        }
    }

    #[Route('/publier/{id}', name: 'publier',requirements: ['id' => '\d+'])]
    public function PublishSortie(int $id, SortieRepository $sortieRepository,EtatRepository $etatRepository, EntityManagerInterface $entityManager )
    {
        $user =$this->getUser();
        $sortie = $sortieRepository->find($id);
        if($sortie != null and $user != null and $sortie->getOrganisateur()->getEmail() == $this->getUser()->getUserIdentifier()){
            $sortie->setEtat($this->etats[1]);
            $entityManager->persist($sortie);
            $entityManager->flush();
        }else{
            $this->addFlash('error','Attention Opération interdite !');
            return $this->redirectToRoute('sortie_home');
        }
        $this->addFlash('success','Les inscriptions sont désormais ouvertes !');
        return $this->redirectToRoute('sortie_home');
    }

    #[Route('/annuler/{id}', name: 'annuler',requirements: ['id' => '\d+'])]
    public function cancelSortie(int $id, SortieRepository $sortieRepository,EntityManagerInterface $entityManager,Request $request ):Response
    {
        $user =$this->getUser();
        $sortie = $sortieRepository->find($id);
        if($sortie != null and $user != null and $sortie->getOrganisateur() == $this->getUser() or $this->security->isGranted('ROLE_ADMIN')){
            $formAnnule = $this->createForm(AnnuleType::class);
            $formAnnule->handleRequest($request);
            if($formAnnule->isSubmitted() && $formAnnule->isValid()){
                $sortie->setMotif($request->get('motif'));
                $sortie->setEtat($this->etats[3]);
                $entityManager->persist($sortie);
                $entityManager->flush();
                $this->addFlash('success','La sortie est annulée !');
                return $this->redirectToRoute('sortie_home');
            }
        }else{
            $this->addFlash('error','Attention Opération interdite !');
            return $this->redirectToRoute('sortie_home');
        }

        return $this->render('sortie/annule.html.twig', [
            'title' => 'Annuler une sortie',
            'sortie'=>$sortie,
            'formAnnule' => $formAnnule->createView()
        ]);
    }

    #[Route('/modifierSortie/{id}', name: 'modifier',requirements: ['id' => '\d+'])]
    public function modifySortie (int $id, Request $request, SortieRepository $sortieRepository,EntityManagerInterface $em) : Response
    {
        $sortie = $sortieRepository->find($id);
        $sortieForm = $this->createForm(SortieType::class,$sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() and $sortieForm->isValid() ){
            $em->persist($sortie);
            $em->flush();
            $this->addFlash('success','La sortie a bien été modifiée !');
            return $this->redirectToRoute('sortie_home');
        }

        return $this->render('sortie/index.html.twig', [
            'title' => 'Modifier une sortie',
            'sortieForm' => $sortieForm->createView(),
        ]);
    }
}
