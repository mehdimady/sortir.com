<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieFilterType;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/sorties', name: 'sorties_')]
class SortieFiltresController extends AbstractController
{
    #[Route('/filtres', name: 'filtres')]
    public function list(Request $request, int $page = 1 , SortieRepository $sortieRepository):Response
    {
        //valeurs par défaut du formulaire de recherche
        //sous forme de tableau associatif, car le form n'est pas associée à une entité
        $searchData = [
            'inscrit' => true,
            'not_inscrit' => true,
            'organisateur' => true,

        ];
        $searchForm = $this->createForm(SortieFilterType::class, $searchData);

        $searchForm->handleRequest($request);

        //on récupère les (éventuelles) données soumises a la mano
        $searchData = $searchForm->getData();

        //appelle ma méthode perso de recherche et filtre
        $paginatedEvents = $sortieRepository->search($page, 20, $this->getUser(), $searchData);

        return $this->render('event/list.html.twig', [
            'paginatedEvents' => $paginatedEvents,
            'searchForm' => $searchForm->createView()
        ]);
    }
}
