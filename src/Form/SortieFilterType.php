<?php

namespace App\Form;

use App\Entity\Campus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
            ->add('Campus',EntityType::class,[
                'label'=>'Campus',
                'class'=>Campus::class,
                'choice_label'=>'name',
                'required'=>false
            ])
            ->add('Recherche', SearchType::class,[
                'label'=>'Le nom de la sortie contient',
                'attr'=>[
                    'placeholder'=>'search'
                ],
                'required'=> false,
            ])
            ->add('date_minimum', DateType::class, [
                'label' => 'Entre le',
                'html5' => false,
                'widget' => 'single_text',
                'attr' => ['class' => 'datepicker'],
                'format' => 'dd/MM/yyyy'
            ])
            ->add('date_max', DateType::class, [
                'label' => 'Et le',
                'html5' => false,
                'widget' => 'single_text',
                'attr' => ['class' => 'datepicker'],
                'format' => 'dd/MM/yyyy'
            ])
            ->add('organisateur', CheckboxType::class, [
                'label' => "Sorties dont je suis l'organisateur/trice",
                'required' => false,
            ])
            ->add('inscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit/e',
                'required' => false,
            ])
            ->add('not_inscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit/e',
                'required' => false,
            ])
            ->add('submit', SubmitType::class, ['label' => 'Rechercher'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
