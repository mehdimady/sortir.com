<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IdenticalTo;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, [
                'required' => true,
                'attr'=>[
                    'class'=>'form-control'
                ]
            ])
            ->add('nom', TextType::class, [
                    'required' => true,
                    'attr'=>[
                        'class'=>'form-control'
                ]
                ])
            ->add('prenom', TextType::class, [
                'required' => true,
                'attr'=>[
                    'class'=>'form-control'
                ]
            ])
            ->add('telephone', TextType::class, [
                'required' => true,
                'attr'=>[
                    'class'=>'form-control'
                ]
            ])
            ->add('email',EmailType::class,[
                'attr'=>[
                    'class'=>'form-control'
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'invalid_message' => "Les deux champs doivent être identiques !",
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez indiquer un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit avoir {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),

                ],
                'first_options'  => ['attr'=>[
                    'class'=>"form-control"
                ]],
                'second_options' => ['attr'=>[
                    'class'=>"form-control"
                ]],
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'required' => true,
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('imageFile', FileType::class, [
                'mapped'=>false,
                'label'=> 'Photo de profil',
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1M',
                        'maxSizeMessage'=>'Taille du fichier maximun de 1mo',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png'
                        ]
                    ])
                ],
                'attr'=>['class'=>"form-control"]

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
