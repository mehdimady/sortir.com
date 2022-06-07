<?php

namespace App\Controller;

use App\Entity\Participant;
use App\services\ImportCSVFile;
use App\Form\InscriptionFileType;
use App\Form\RegistrationFormType;
use App\Form\SearchVilleType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
#[IsGranted('ROLE_ADMIN')]
#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'tous')]
    public function displayAll(
        ParticipantRepository $participantRepository,
        Request $request,EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher,
        SluggerInterface $slugger,ImportCSVFile $csv): Response
    {
        $participants = $participantRepository->findAll();
        $formSearch = $this->createForm(SearchVilleType::class);
        $formSearch->handleRequest($request);
        if($formSearch->isSubmitted() && $formSearch->isValid()){
            $participants = $participantRepository->searchParticipant($request->get('search'));
        }
        $formFile = $this->createForm(InscriptionFileType::class);
        $formFile->handleRequest($request);
        if($formFile->isSubmitted() && $formFile->isValid()){
            $filecsv = $formFile->get('upload_file')->getData();
            $filecsv->move(
                $this->getParameter('image_directory'),
                "participant.csv");
            $csv->test($entityManager);
            $this->addFlash('success','Les inscriptions sont enregistrées !');
            return $this->redirectToRoute('admin_tous');
        }
        $user = new Participant();
        $addForm = $this->createForm(RegistrationFormType::class, $user);
        $addForm->handleRequest($request);
        if ($addForm->isSubmitted() && $addForm->isValid()) {
            $uploadedFile = $addForm->get('imageFile')->getData();
            if ($uploadedFile){
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();
                $uploadedFile->move(
                    $this->getParameter('image_directory'),
                    $newFilename);
            }else{
                $newFilename = 'noimage.jpg';
            }
            $user->setRoles(["ROLE_USER"]);
            $user->setImageFilename($newFilename);
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $addForm->get('plainPassword')->getData()
                )
            );
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success','Le compte a bien été créé!');
            return $this->redirectToRoute('admin_tous');
        }
        return $this->render('admin/admin.html.twig', [
            'title' => 'Gestion des participants',
            'subtitle' => 'Ajouter un compte',
            "participants"=>$participants,
            'formSearch'=>$formSearch->createView(),
            'adminForm' => $addForm->createView(),
            'formFile'=>$formFile->createView()
        ]);
    }

    #[Route('/delete/{id}', name: 'delete',requirements: ['id' => '\d+'])]
    public function removeParticipant(ParticipantRepository $participantRepository,Participant $participant,
                                 EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($participant);
        $entityManager->flush();
        $this->addFlash('success','Le compte a bien été supprimé!');
        return $this->redirectToRoute('admin_tous');
    }

    #[Route('/disable/{id}', name: 'disable',requirements: ['id' => '\d+'])]
    public function disableParticipant(int $id, ParticipantRepository $participantRepository, Participant $participant,
                                       EntityManagerInterface $entityManager){

        $participant = $participantRepository->find($id);
        $participant->setActif(false);
        $entityManager->flush();
        $this->addFlash('success','Le compte a bien été désactivé!');
        return $this->redirectToRoute('admin_tous');

    }

    #[Route('/enable/{id}', name: 'enable',requirements: ['id' => '\d+'])]
    public function enableParticipant(int $id,ParticipantRepository $participantRepository, Participant $participant,
                                      EntityManagerInterface $entityManager){
        $participant = $participantRepository->find($id);
        $participant->setActif(true);
        $entityManager->flush();
        $this->addFlash('success','Le compte a bien été activé!');
        return $this->redirectToRoute('admin_tous');
    }

}
