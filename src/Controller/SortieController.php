<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Form\AnnuleType;
use App\Form\SortieFilterType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\services\GestionDesEtats;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Sortie;
use App\Form\SortieType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
#[IsGranted('ROLE_USER')]
#[Route('/sortie', name: 'sortie_')]
class SortieController extends AbstractController
{
    /* Liste des index des  états =  0:'En création',1: 'Ouvert',2: 'Fermé',3: 'Annulé',4: 'En cours',5: 'Terminé',6: 'Historisé' */
    private array $etats;
    private Security $security;

    public function __construct(EtatRepository $repo, Security $security){
        $this->etats = $repo->findAll();
        $this->security = $security;
    }

    #[Route('/', name: 'home')]
    public function displayAllEvents(
        Request $request,
        SortieRepository $sortieRepository,
        EtatRepository $etatRepository,
        GestionDesEtats $gestionDesEtats,
        EntityManagerInterface $manager): Response
    {
        $gestionDesEtats->UpdateStatesOfEvents($sortieRepository, $etatRepository, $manager);

        $formSearch = $this->createForm(SortieFilterType::class);
        $formSearch->handleRequest($request);

        if ($formSearch->isSubmitted() and $formSearch->isValid()) {
            $result = $sortieRepository->search($this->getUser(),$formSearch->getData());
        }else{
            $result = $sortieRepository->displayByDefault($this->getUser());
        }

//    Si app.user est inscrit a la sortie
        $sortiesReturn = [];
        foreach($result as $sortie){
            $inscrit =  false ;
            foreach ($sortie->getParticipants() as $participant) {
                if ($this->getUser()->getUserIdentifier() == $participant->getEmail()) {
                    $inscrit = true;
                }
            }
            $sortiesReturn[] = ['sortie' => $sortie, 'inscrit' => $inscrit];
        }


        return $this->render('sortie/home.html.twig', [
            'title' => 'Campus | sorties',
            'result'=>$result,
            'sorties' => $sortiesReturn,
            'formSearch'=>$formSearch->createView()
        ]);
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
        $sortieForm = $this->createForm(SortieType::class,$sortie);
        $sortieForm->handleRequest($request);
            if($sortieForm->get('enregistre')->isClicked()){
                $sortie->setEtat($etats[0]);
                $message = 'La sortie a bien été créée !';
            }else{
                $sortie->setEtat($etats[1]);
                $message = 'La sortie a bien été créée et publiée !';
            }

        if ($sortieForm->isSubmitted() and $sortieForm->isValid() ){
            $dateDebut = clone ($sortie->getDateHeureDebut());
            $sortie->setDateHeureFin($dateDebut->modify("+{$sortie->getDuree()} minutes"));
            $em->persist($sortie);
            $em->flush();
            $this->addFlash('success',$message);
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
            $sortie = $sortieRepository->find($id);
            $sortieLibelle = $sortie->getEtat()->getLibelle();
            $maxInscrit = $sortie->getNbInscriptionsMax();
            if ($sortieLibelle === 'Ouvert' and count($sortie->getParticipants()) < $maxInscrit){
                $sortie->addParticipant($this->getUser());
                if ( count($sortie->getParticipants()) === $maxInscrit){
                    $sortie->setEtat($this->etats[3]);
                }
                $entityManager->persist($sortie);
                $entityManager->flush();
                $this->addFlash('success','Vous êtes inscrit !');
                return $this->redirectToRoute('sortie_home');
            }
            else{
                $this->addFlash('warning','Vous ne pouvez pas vous inscrire à cette sortie !');
                return $this->redirectToRoute('sortie_home');
            }
    }

    #[Route('/desister/{id}', name: 'desister',requirements: ['id' => '\d+'])]
    public function removeSortie(int $id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager )
    {
            $sortie = $sortieRepository->find($id);
            $sortieLibelle = $sortie->getEtat()->getLibelle();
            $sortieUser =$sortie->getParticipants()->contains($this->getUser());
            if ($sortieUser and $sortieLibelle == 'Ouvert' or $sortieLibelle == 'Fermé'){
                $sortie->removeParticipant( $this->getUser());
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
                $this->addFlash('warning',
                    'Vous ne pouvez pas vous désinscrire si la sortie est dans un autre état que Ouvert ou Fermé ou inscrivez vous !');
                return $this->redirectToRoute('sortie_home');
            }
    }

    #[Route('/publier/{id}', name: 'publier',requirements: ['id' => '\d+'])]
    public function PublishSortie(int $id, SortieRepository $sortieRepository,EtatRepository $etatRepository, EntityManagerInterface $entityManager )
    {
        $user =$this->getUser();
        $sortie = $sortieRepository->find($id);
        if($sortie != null and $user != null
            and $sortie->getOrganisateur()->getEmail() == $this->getUser()->getUserIdentifier()){
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
        $sortie = $sortieRepository->find($id);
        $sortieLibelle = $sortie->getEtat()->getLibelle();
        $sortieGetUser = $sortie->getOrganisateur() == $this->getUser() ||  $this->security->isGranted('ROLE_ADMIN');
        $sortieEtat = ($sortieLibelle == 'Ouvert' or $sortieLibelle == 'Fermé' or $sortieLibelle == 'En création');
        if($sortie != null and $sortieGetUser and $sortieEtat){
            $formAnnule = $this->createForm(AnnuleType::class,$sortie);
            $formAnnule->handleRequest($request);
            if($formAnnule->isSubmitted() && ($formAnnule->isValid())){
                $sortie->setEtat($this->etats[3]);
                $entityManager->persist($sortie);
                $entityManager->flush();
                $this->addFlash('success','La sortie est annulée !');
                return $this->redirectToRoute('sortie_home');
            }
        }else{
            $this->addFlash('error','Vous ne pouvez annuler que les sorties ouvertes, fermées ou en création !');
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
        $sortieLibelle = $sortie->getEtat()->getLibelle();
        $sortieGetUser = $sortie->getOrganisateur() === $this->getUser() or $this->security->isGranted('ROLE_ADMIN');
        if ($sortie != null and $sortieLibelle == 'En création' and $sortieGetUser){
            $sortieForm = $this->createForm(SortieType::class,$sortie);
            $sortieForm->handleRequest($request);

            if ($sortieForm->isSubmitted() and $sortieForm->isValid() ){
                $em->persist($sortie);
                $em->flush();
                $this->addFlash('success','La sortie a bien été modifiée !');
                return $this->redirectToRoute('sortie_home');
            }
        }
        else{
            $this->addFlash('error','Vous ne pouvez pas modifier cette sortie !');
            return $this->redirectToRoute('sortie_home');
        }


        return $this->render('sortie/index.html.twig', [
            'title' => 'Modifier une sortie',
            'sortieForm' => $sortieForm->createView(),
            'modifier'=>true
        ]);
    }

}
