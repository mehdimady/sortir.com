<?php

namespace App\Form;


use App\Entity\Sortie;


use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Form\AbstractType;
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
            ->add('nom')
            ->add('dateHeureDebut')
            ->add('dateLimiteInscription')
            ->add('duree')
            ->add('nbInscriptionsMax')
            ->add('infosSortie')
            ->add('campus', TextType::class, [
                'disabled' => true,
                'mapped' => false,
                'data' => $this->security->getUser()->getCampus(),
            ])
            ->add('lieux',LieuType::class,[
                'label'=> ' ',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
