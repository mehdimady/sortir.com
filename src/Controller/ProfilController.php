<?php

namespace App\Controller;

use App\Form\RegistrationFormType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    #[Route('/profil/{id}', name: 'app_profil')]
    public function myProfil(int $id, ParticipantRepository $participantRepository): Response
    {
        $participant = $participantRepository->find($id);
        if (!$participant) {
            throw $this->createNotFoundException('Profil indisponible !');
        }
        return $this->render('profil/profil.html.twig', [
            'title' => 'Mon Profil',
            "participant" => $participant
        ]);

    }

    #[Route('/modifier/{id}', name:"app_modifier")]
    public function Modify(int $id, Request $request, EntityManagerInterface $entityManager, ParticipantRepository $participantRepository ): Response
    {
        $participant = $participantRepository->findOneBy(['id' => $id]);
        $participantForm = $this->createForm(RegistrationFormType::class, $participant);
        $participantForm->handleRequest($request);
        if ($participantForm->isSubmitted() && $participantForm->isValid()) {
            $entityManager->persist($participant);
            $entityManager->flush();

            $this->addFlash('Bravo', 'Le profil a été modifié !');
            return $this->redirectToRoute('app_profil', ['id' => $participant->getId()]);
        }
        return $this->render('registration/register.html.twig', [
            'title' => 'Modifier le profil',
            'subtitle' => 'Mon Profil',
            "participant" => $participant,
            'registrationForm' => $participantForm->createView()
        ]);
    }
}
