<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class InscriptionFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('upload_file', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => true,
                'attr'=>[
                    'class'=>'form-control form-control-sm'
                ],'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'text/x-comma-separated-values',
                            'text/comma-separated-values',
                            'text/x-csv',
                            'text/csv',
                            'text/plain',
                            'application/octet-stream',
                            'application/vnd.ms-excel',
                            'application/x-csv',
                            'application/csv',
                            'application/excel',
                            'application/vnd.msexcel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                        ],
                        'mimeTypesMessage' => "This document isn't valid."

                    ])
                ],
            ])
            ->add('Integrer', SubmitType::class,[
                'attr'=>[
                    'class'=>'btn bg-4 btn-md text-white f-lunatic btn-connexion'
                ]
            ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([

        ]);
    }
}
