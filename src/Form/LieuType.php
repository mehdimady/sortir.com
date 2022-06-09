<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TextType::class,[ 'attr' => [
                'class' =>"form-control",
                'placeholder'=>'mini 3 max 50'
            ]])
            ->add('rue',TextType::class,[ 'attr' => [
        'class' => "form-control",
                'placeholder'=>'mini 3 max 255'
    ]])
            ->add('latitude',NumberType::class,[ 'attr' => [
                'class' =>"form-control",
                'placeholder'=>'99.999999'
            ]])
            //todo corriger problème message
            ->add('longitude',NumberType::class,[ 'attr' => [
                'class' =>"form-control",
                'placeholder'=>'99.999999'
            ]])
            //todo corriger problème message
            ->add('ville', EntityType::class, [
                'class'=>Ville::class,
                'choice_label'=>'nom',
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
