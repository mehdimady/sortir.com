<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('dateHeureDebut')
            ->add('dateLimiteInscription')
            ->add('duree')
            ->add('nbInscriptionsMax')
            ->add('infosSortie')
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'required' => true
            ])
            ->add('ville', EntityType::class, [
                'mapped'=>false,
                'label'=>'Ville',
                'class'=>Ville::class,
                'choice_label'=>'nom'
            ])
            ->add('lieux', EntityType::class, [

                'label'=>'Lieu',
                'class'=>Lieu::class,
                'choice_label'=>'nom'
            ])
            ->add('rue', EntityType::class, [
                'mapped'=>false,
                'label'=>'Rue',
                'class'=>Lieu::class,
                'choice_label'=>'rue'
            ])
            ->add('codepostal', EntityType::class, [
                'mapped'=>false,
                'label'=>'Code Postal',
                'class'=>Ville::class,
                'choice_label'=>'codePostal'
            ])
            ->add('lattitude', TextType::class, [
                'mapped'=>false,
                'attr' => ['class' => Lieu::class],
            ])
            ->add('longitude', TextType::class, [
                'mapped'=>false,
                'attr' => ['class' => Lieu::class],
            ]);



    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
