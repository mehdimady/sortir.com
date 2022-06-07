<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusType;
use App\Form\SearchVilleType;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[IsGranted('ROLE_ADMIN')]
#[Route('/campus', name: 'campus_')]
class CampusController extends AbstractController
{
    #[Route('/', name: 'tous')]
    public function displayAll(CampusRepository $campusRepository,Request $request, EntityManagerInterface $entityManager): Response
    {
        $campusx = $campusRepository->findAll();
        $campus = new Campus();
        $formCampus = $this->createForm(CampusType::class,$campus);
        $formCampus->handleRequest($request);
        if($formCampus->isSubmitted() && $formCampus->isValid()){
            $entityManager->persist($campus);
            $entityManager->flush();

            $this->addFlash('success','Le campus a bien été créé!');
            return $this->redirectToRoute('campus_tous');
        }

        $formSearch = $this->createForm(SearchVilleType::class);
        $formSearch->handleRequest($request);
        if($formSearch->isSubmitted() && $formSearch->isValid()){
            $campusx = $campusRepository->searchCampus($request->get('search'));
        }

        return $this->render('campus/campus.html.twig', [
            'title' => 'Les Campus',
            "campusx"=>$campusx,
            "button"=>"Ajouter",
            'formCampus'=>$formCampus->createView(),
            'formSearch'=>$formSearch->createView()
        ]);
    }

    #[Route('/delete/{id}', name: 'delete',requirements: ['id' => '\d+'])]
    public function removeCampus(CampusRepository $campusRepository,Campus $campus,EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($campus);
        $entityManager->flush();
        return $this->redirectToRoute('campus_tous');
    }

    #[Route('/modify/{id}', name: 'modify',requirements: ['id' => '\d+'])]
    public function modifyCampus(Request $request,CampusRepository $campusRepository,int $id,EntityManagerInterface $entityManager): Response
    {
        $campusx =  $campusRepository->findAll();
        $campus = $campusRepository->find($id);
        $formCampus = $this->createForm(CampusType::class,$campus);
        $formCampus->handleRequest($request);
        if($formCampus->isSubmitted() && $formCampus->isValid()){
            $entityManager->persist($campus);
            $entityManager->flush();

            $this->addFlash('success','Le campus a bien été modifié !');
            return $this->redirectToRoute('campus_tous');
        }

        $formSearch = $this->createForm(SearchVilleType::class);
        $formSearch->handleRequest($request);
        if($formSearch->isSubmitted() && $formSearch->isValid()){
            $campusx = $campusRepository->searchCampus($request->get('search'));
        }


        return $this->render('campus/campus.html.twig', [
            'title' => 'Les Campus',
            "campusx"=>$campusx,
            "button"=>"Modifier",
            'formCampus'=>$formCampus->createView(),
            'formSearch'=>$formSearch->createView()
        ]);


    }
}
