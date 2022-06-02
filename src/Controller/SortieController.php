<?php

namespace App\Controller;

use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Sortie;
use App\Form\SortieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie', name: 'sortie_')]
class SortieController extends AbstractController
{

    #[Route('/{id}', name: 'affiche',requirements: ['id' => '\d+'])]
    public function DisplayOne(SortieRepository $sortieRepository,int $id): Response    {
        $sortie = $sortieRepository->findOneBy(["id"=>$id]);

        return $this->render('sortie/affiche.html.twig', [
            'title' => "Afficher une sortie",
            "sortie" =>$sortie
        ]);
    }


    #[Route('/create', name: 'create')]
    public function createSortie(Request $request, EntityManagerInterface $em ): Response
    {
        $user =$this->getUser();

        $sortie =new Sortie();
        $sortie->setOrganisateur($user);
        $sortie->setCampus($user->getCampus());

        $sortieForm = $this->createForm(SortieType::class,$sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() and $sortieForm->isValid() ){
            $em->persist($sortie);
            $em->flush();
        }

        return $this->render('sortie/index.html.twig', [
            'title' => 'Créer une sortie',
            'sortieForm' => $sortieForm->createView(),
        ]);
    }

    #[Route('/inscrire/{id}', name: 'inscrire',requirements: ['id' => '\d+'])]
    public function RegisterSortie(int $id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager )
    {
        $user = $this->getUser();
        if ($user!=null){
            $sortie = $sortieRepository->findOneBy(["id"=>$id]);
            $sortie->addParticipant($user);

//            todo controlle de l'etat
            //  si c'est le dernier participant => alors on passe en fermée

            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success','Vous êtes inscrit !');
            return $this->redirectToRoute('app_home');
        }
        else{
            $this->addFlash('warning','Veuillez vous connecter !');
            return $this->redirectToRoute('app_home');
        }
    }

    #[Route('/desister/{id}', name: 'desister',requirements: ['id' => '\d+'])]
    public function removeSortie(int $id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager )
    {
        $user = $this->getUser();
        if ($user!=null){
            $sortie = $sortieRepository->findOneBy(["id"=>$id]);
            $sortie->removeParticipant($user);

//             todo controlle de l'etat
//  si c'est le dernier participant =>  si la date de fin d'inscription est dépassé alors reste en fermée
//                                                  =>  si la date de fin d'inscription n'est pas dépassé alors repasse en ouverte

            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success','Vous êtes désinscrit !');
            return $this->redirectToRoute('app_home');
        }
        else{
            $this->addFlash('warning','Veuillez vous connecter !');
            return $this->redirectToRoute('app_home');
        }
    }
}
