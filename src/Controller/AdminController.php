<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\RegistrationFormType;
use App\Form\SearchVilleType;
use App\Repository\ParticipantRepository;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'tous')]
    public function displayAll(ParticipantRepository $participantRepository,Request $request,
                               EntityManagerInterface $entityManager,  UserPasswordHasherInterface $userPasswordHasher,
                               UserAuthenticatorInterface $userAuthenticator, AppAuthenticator $authenticator,
                               SluggerInterface $slugger): Response
    {
        $participants = $participantRepository->findAll();
        $formSearch = $this->createForm(SearchVilleType::class);
        $formSearch->handleRequest($request);
        if($formSearch->isSubmitted() && $formSearch->isValid()){
            $participants = $participantRepository->searchParticipant($request->get('search'));
        }

        #FORMULAIRE

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
            }
            else{
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
            // do anything else you need here, like send an email

            $this->addFlash('success','Le compte a bien été créé!');
            return $this->redirectToRoute('admin_tous');
        }

        return $this->render('admin/admin.html.twig', [
            'title' => 'Gestion des participants',
            "participants"=>$participants,
            'formSearch'=>$formSearch->createView(),
            'adminForm' => $addForm->createView()
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
    public function disableParticipant(){

    }

}