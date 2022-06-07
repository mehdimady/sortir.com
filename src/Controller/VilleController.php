<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\SearchVilleType;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[IsGranted('ROLE_ADMIN')]
#[Route('/ville', name: 'ville_')]
class VilleController extends AbstractController
{
    #[Route('s', name: 'toutes')]
    public function displayAll(VilleRepository $villeRepository,Request $request, EntityManagerInterface $entityManager): Response
    {
        $villes =  $villeRepository->findAll();
       $ville = new Ville();
       $formVille = $this->createForm(VilleType::class,$ville);
        $formVille->handleRequest($request);
        if($formVille->isSubmitted() && $formVille->isValid()){
            $entityManager->persist($ville);
            $entityManager->flush();

            $this->addFlash('success','La ville a bien été créée !');
            return $this->redirectToRoute('ville_toutes');
        }

        $formSearch = $this->createForm(SearchVilleType::class);
        $formSearch->handleRequest($request);
        if($formSearch->isSubmitted() && $formSearch->isValid()){
            $villes = $villeRepository->searchVille($request->get('search'));
        }

        return $this->render('ville/villes.html.twig', [
            'title' => 'Les Villes',
            "villes"=>$villes,
            "button"=>"Ajouter",
            'formVille'=>$formVille->createView(),
            'formSearch'=>$formSearch->createView()
        ]);
    }

    #[Route('/delete/{id}', name: 'delete',requirements: ['id' => '\d+'])]
    public function removeVille(VilleRepository $villeRepository,Ville $ville,EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($ville);
        $entityManager->flush();
        return $this->redirectToRoute('ville_toutes');
    }

    #[Route('/modify/{id}', name: 'modify',requirements: ['id' => '\d+'])]
    public function modifyVille(Request $request,VilleRepository $villeRepository,int $id,EntityManagerInterface $entityManager): Response
    {
        $villes =  $villeRepository->findAll();
        $ville = $villeRepository->find($id);
        $formVille = $this->createForm(VilleType::class,$ville);
        $formVille->handleRequest($request);
        if($formVille->isSubmitted() && $formVille->isValid()){
            $entityManager->persist($ville);
            $entityManager->flush();
            $this->addFlash('success','La ville a bien été modifiée !');
            return $this->redirectToRoute('ville_toutes');
        }
        $formSearch = $this->createForm(SearchVilleType::class);
        $formSearch->handleRequest($request);
        if($formSearch->isSubmitted() && $formSearch->isValid()){
            $villes = $villeRepository->searchVille($request->get('search'));
        }
        return $this->render('ville/villes.html.twig', [
            'title' => 'Les Villes',
            "villes"=>$villes,
            "button"=>"Modifier",
            'formVille'=>$formVille->createView(),
            'formSearch'=>$formSearch->createView()
        ]);
    }
}
