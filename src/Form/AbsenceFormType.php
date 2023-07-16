<?php

namespace App\Form;

use App\Entity\Absence;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AbsenceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('dateAbsence', DateType::class, [
            'widget' => 'choice',
            'years' => range(date('Y'), date('Y') - 23),
            'attr' => [
                'class' => 'form-control',
                'name' => 'dateAbsence',
            ],
        ])

        ->add('heureDebut', TimeType::class, [
            'widget' => 'choice',
            'attr' => [
                'class' => 'form-control',
                'name' => 'heureDebut',
            ],
        ])

        ->add('heureFin', TimeType::class, [
            'widget' => 'choice',
            'attr' => [
                'class' => 'form-control',
                'name' => 'heureFin',
            ],
        ]);
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Absence::class,
        ]);
    }
}
