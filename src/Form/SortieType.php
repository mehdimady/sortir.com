<?php

namespace App\Form;


use App\Entity\Sortie;


use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class SortieType extends AbstractType
{
    protected $security;

    public function __construct(Security $security,EntityManagerInterface $em){
        return $this->security = $security ;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TextType::class,[
                'attr'=>[
                    'class'=>'form-control',
                    'placeholder'=>'mini 3 max 50 caractères'
                ]
            ])

            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => 'Entre le',
                'widget' => 'single_text',
                'html5' => true,
                'required'=>true,
                'attr'=>[
                    'class'=>'form-control',
                ],
            ])
            ->add('dateLimiteInscription', DateTimeType::class, [
                'label' => 'Entre le',
                'widget' => 'single_text',
                'html5' => true,
                'required'=>true,
                'attr'=>[
                    'class'=>'form-control'
                ],
            ])
            ->add('duree',NumberType::class,[
                'attr'=>[
                    'class'=>'form-control',
                    'placeholder'=>'mini 30 minutes'
                ]
            ])
            ->add('nbInscriptionsMax',NumberType::class,[
                'attr'=>[
                    'class'=>'form-control',
                     'placeholder'=>'mini 2 participants'
                ]
            ])
            ->add('infosSortie',TextareaType::class,[
                'attr'=>[
                    'class'=>'form-control',
                     'placeholder'=>'Informations sur la sortie',
                     'rows'=>'5',
                ]
            ])
            ->add('campus', TextType::class, [
                'disabled' => true,
                'mapped' => false,
                'data' => $this->security->getUser()->getCampus(),
                'attr'=>[
                    'class'=>'form-control'
                ]
            ])
            ->add('lieux',LieuType::class)

            ->add('enregistre', SubmitType::class,
                ['label' => 'Enregistrer',
                    'attr'=>[
                        'class'=>'btn bg-4 btn-lg text-white f-lunatic btn-connexion'
                    ]
                ])
            ->add('publie', SubmitType::class,
                ['label' => 'Publier',
                    'attr'=>[
                        'class'=>'btn bg-4 btn-lg text-white f-lunatic btn-connexion'
                    ]
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
