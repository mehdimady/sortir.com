<?php

namespace App\Controller;

use App\Repository\SortieRepository;
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
}
