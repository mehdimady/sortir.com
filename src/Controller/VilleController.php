<?php

namespace App\Controller;

use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/ville', name: 'ville_')]
class VilleController extends AbstractController
{
    #[Route('s', name: 'toutes')]
    public function displayAll(VilleRepository $villeRepository): Response
    {
       $villes =  $villeRepository->findAll();
        return $this->render('ville/villes.html.twig', [
            'title' => 'Les Villes',
            "villes"=>$villes
        ]);
    }
}
