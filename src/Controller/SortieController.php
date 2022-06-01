<?php

namespace App\Controller;

use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/sortie', name: 'sortie_')]
class SortieController extends AbstractController
{
    #[Route('/{id}', name: 'affiche',requirements: ['id' => '\d+'])]
    public function DisplayOne(SortieRepository $sortieRepository,int $id): Response
    {
        $sortie = $sortieRepository->findOneBy(["id"=>$id]);

        return $this->render('sortie/affiche.html.twig', [
            'title' => "Afficher une sortie",
            "sortie" =>$sortie
        ]);
    }

    #[Route('/inscrire/{id}', name: 'inscrire',requirements: ['id' => '\d+'])]
    public function RegisterSortie(int $id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager )
    {
        $user = $this->getUser();
        if ($user!=null){
            $sortie = $sortieRepository->findOneBy(["id"=>$id]);
            $sortie->addParticipant($user);
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
