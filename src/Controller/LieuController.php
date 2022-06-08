<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Form\SearchVilleType;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[IsGranted('ROLE_USER')]
#[Route('/lieu', name: 'lieu_')]
class LieuController extends AbstractController
{
    #[Route('x', name: 'tous')]
    public function displayAll(LieuRepository $lieuRepository,Request $request, EntityManagerInterface $entityManager): Response
    {
        $lieux =  $lieuRepository->findAll();
        $lieu = new Lieu();
        $formLieu = $this->createForm(LieuType::class,$lieu);
        $formLieu->handleRequest($request);
        if($formLieu->isSubmitted() && $formLieu->isValid()){
            $entityManager->persist($lieu);
            $entityManager->flush();

            $this->addFlash('success','Le lieu a bien été créé !');
            return $this->redirectToRoute('lieu_tous');
        }

        $formSearch = $this->createForm(SearchVilleType::class);
        $formSearch->handleRequest($request);
        if($formSearch->isSubmitted() && $formSearch->isValid()){
            $lieux = $lieuRepository->searchLieu($request->get('search'));
        }

        return $this->render('lieu/lieu.html.twig', [
            'title' => 'Les Lieux',
            "lieux"=>$lieux,
            "button"=>"Ajouter une ville",
            'formLieu'=>$formLieu->createView(),
            'formSearch'=>$formSearch->createView()
        ]);
    }

    #[Route('/delete/{id}', name: 'delete',requirements: ['id' => '\d+'])]
    public function removeLieu(LieuRepository $lieuRepository,Lieu $lieu,EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($lieu);
        $entityManager->flush();
        return $this->redirectToRoute('lieu_tous');
    }

    #[Route('/modify/{id}', name: 'modify',requirements: ['id' => '\d+'])]
    public function modifyLieu(Request $request,LieuRepository $lieuRepository,int $id,EntityManagerInterface $entityManager): Response
    {
        $lieux = $lieuRepository->findAll();
        $lieu = $lieuRepository->find($id);
        $formLieu = $this->createForm(LieuType::class, $lieu);
        $formLieu->handleRequest($request);
        if ($formLieu->isSubmitted() && $formLieu->isValid()) {
            $entityManager->persist($lieu);
            $entityManager->flush();
            $this->addFlash('success', 'Le lieu a bien été modifié !');
            return $this->redirectToRoute('lieu_tous');
        }
        $formSearch = $this->createForm(SearchVilleType::class);
        $formSearch->handleRequest($request);
        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $lieux = $lieuRepository->searchLieu($request->get('search'));
        }
        return $this->render('lieu/lieu.html.twig', [
            'title' => 'Les Lieux',
            "lieux" => $lieux,
            "button" => "Modifier",
            'formLieu' => $formLieu->createView(),
            'formSearch' => $formSearch->createView()
        ]);
    }
}
