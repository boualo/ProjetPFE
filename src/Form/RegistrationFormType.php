<?php

namespace App\Form;

use App\Entity\Admin;
use App\Validator\Constraints;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'constraints' => [
                    new Constraints(),
                    new Length([
                        'max' => 20,
                        'maxMessage' => 'Le nom ne peut pas dépasser 20 caractères.',
                    ]),
                ],
                'label' => 'Nom',
                'attr' => [
                    'class' => 'form-control',
                    'name' => 'nom',
                ],
            ])

            ->add('prenom', TextType::class, [
                'constraints' => [
                    new Constraints(),
                    new Length([
                        'max' => 20,
                        'maxMessage' => 'Le nom ne peut pas dépasser 20 caractères.',
                    ]),
                ],
                'label' => 'Prénom',
                'attr' => [
                    'class' => 'form-control',
                    'name' => 'prenom',
                ],
            ])
            
            ->add('adresse', TextType::class, [
                'constraints' => [
                    new Length([
                        'max' => 100,
                        'maxMessage' => 'L\'adresse ne peut pas dépasser 100 caractères.',
                    ]),
                ],
                'label' => 'Adresse',
                'attr' => [
                    'class' => 'form-control',
                    'name' => 'adresse',
                ],
            ])
            ->add('tel', TextType::class, [
                'constraints' => [
                    new Regex([
                        'pattern' => '/^0[0-9]{9}$/',
                        'message' => 'Veuillez entrer un numéro de téléphone valide.',
                    ]),
                    new Length([
                        'max' => 10,
                        'maxMessage' => 'Le téléphone ne peut pas dépasser 10 caractères.',
                    ]),
                ],
                'label' => 'Téléphone',
                'attr' => [
                    'class' => 'form-control',
                    'name' => 'tel',
                ],
            ])
            ->add('CIN', TextType::class,[
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[A-Z]{1,2}[0-9]{5,7}$/',
                        'message' => 'Veuillez entrer un CIN valide.',
                    ]),
                ],
                'label' => 'CIN',
                'attr' => [
                    'class' => 'form-control',
                    'name' => 'CIN',
                ],
            ])
            
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Length([
                        'max' => 30,
                        'maxMessage' => 'L\'email ne peut pas dépasser 30 caractères.',
                    ]),
                ],
                'label' => 'Email',
                'attr' => [
                    'class' => 'form-control',
                    'name' => 'email',
                ],
            ])

            ->add('sexe',ChoiceType::class, [
                'label' => 'Sexe',
                'choices' => [
                    'Femme' => 'F',
                    'Homme' => 'H'

                ],
                'placeholder' => 'Sélectionnez une option',
                'attr' => [
                    'class' => 'form-control',
                    'name' => 'sexe'
                ]
            ])
            ->add('photo', FileType::class, [
                'label' => 'Changer l\'image:',
                'attr' => [
                    'name' => 'photo',
                    'class' => 'form-control'
                ],
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide !',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Admin::class,
        ]);
    }
}
