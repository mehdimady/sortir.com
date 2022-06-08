<?php

namespace App\Controller;

use App\Form\RegistrationFormType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
#[IsGranted('ROLE_USER')]
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
    public function modify(int $id, Request $request, EntityManagerInterface $entityManager,
                           ParticipantRepository $participantRepository, SluggerInterface $slugger,
                           UserPasswordHasherInterface $userPasswordHasher ): Response
    {
        $user = $this->getUser();
        $participant = $participantRepository->find($id);
        $participantForm = $this->createForm(RegistrationFormType::class, $participant);
        $participantForm->handleRequest($request);
            if ($user!=null and $user->getUserIdentifier()==$participant->getUserIdentifier()){
                if ($participantForm->isSubmitted() && $participantForm->isValid()) {
                    $uploadedFile = $participantForm->get('imageFile')->getData();
                    if ($uploadedFile) {
                        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                        $safeFilename = $slugger->slug($originalFilename);
                        $newFilename = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();
                        $uploadedFile->move(
                            $this->getParameter('image_directory'),
                            $newFilename);
                        if (file_exists('./uploads/image/'.$user->getImageFilename())) {
                            if ($user->getImageFilename() != 'noimage.jpg' and $user->getImageFilename() !=""){
                                unlink('./uploads/image/'.$user->getImageFilename());
                            }
                        }
                        $user->setImageFilename($newFilename);
                    }
                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user,
                            $participantForm->get('plainPassword')->getData()
                        )
                    );
                    $entityManager->persist($participant);
                    $entityManager->flush();

                    $this->addFlash('success', 'Le profil a été modifié !');
                    return $this->redirectToRoute('app_profil', ['id' => $participant->getId()]);
                }
            }
            else{
                $this->addFlash('error', 'Vous ne pouvez pas accéder à cette page !');
                return $this->redirectToRoute('sortie_home');
            }
            return $this->render('registration/register.html.twig', [
                'title' => 'Modifier le profil',
                'subtitle' => 'Modifier mon Profil',
                "participant" => $participant,
                'registrationForm' => $participantForm->createView()
            ]);
        }
    }
