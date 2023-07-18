<?php

namespace App\Form;

use App\Entity\Enseignant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnseignantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password')
            ->add('nom' , TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'class' => 'form-control',
                    'name' => 'nom',
                ],
            ])
            ->add('prenom' , TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'class' => 'form-control',
                    'name' => 'nom',
                ],
            ])
            ->add('adresse' , TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'class' => 'form-control',
                    'name' => 'nom',
                ],
            ])
            ->add('tel' , TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'class' => 'form-control',
                    'name' => 'nom',
                ],
            ])
            ->add('sexe')
            ->add('CIN' , TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'class' => 'form-control',
                    'name' => 'nom',
                ],
            ])
            ->add('email' , TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'class' => 'form-control',
                    'name' => 'nom',
                ],
            ])
            ->add('photo')
            ->add('matiere' , TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'class' => 'form-control',
                    'name' => 'nom',
                ],
            ])
            ->add('groupe')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Enseignant::class,
        ]);
    }
}
