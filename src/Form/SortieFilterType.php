<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus',EntityType::class,[
                'label'=>'campus',
                'class'=>Campus::class,
                'choice_label'=>'nom',
                'required'=>false,
                'attr'=>[
                    'class'=>'form-control'
                ],
            ])
            ->add('recherche', SearchType::class,[
                'label'=>'Le nom de la sortie contient',
                'attr'=>[
                    'class'=>'form-control'
                ],
                'required'=> false,
            ])
            ->add('date_minimum', DateType::class, [
                'label' => 'Entre le',
                'widget' => 'single_text',
                'html5' => true,
                'required'=>false,
                'attr'=>[
                    'class'=>'form-control'
                ],
            ])
            ->add('date_max', DateType::class, [
                'label' => 'Et le',
                'widget' => 'single_text',
                'html5' => true,
                'required'=>false,
                'attr'=>[
                    'class'=>'form-control'
                ],
            ])
            ->add('organisateur', CheckboxType::class, [
                'label' => "Sorties dont je suis l'organisateur/trice",
                'required' => false,
                'attr'=>[
                    'class'=>'form-check-input'
                ],
            ])
            ->add('inscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit/e',
                'required' => false,
                'attr'=>[
                    'class'=>'form-check-input'
                ],
            ])
            ->add('not_inscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit/e',
                'required' => false,
                'attr'=>[
                    'class'=>'form-check-input'
                ],
            ])
            ->add('passe', CheckboxType::class, [
                'label' => 'Sorties passées',
                'required' => false,
                'attr'=>[
                    'class'=>'form-check-input'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([

        ]);
    }
}
