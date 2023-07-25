<?php

namespace App\Form;

use App\Entity\Eleve;
use App\Validator\Constraints;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Regex;
class EleveFormType extends AbstractType
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
                        'maxMessage' => "Le nom ne peut pas dépasser 20 caractères.",
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
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control',
                    'autocomplete' => 'new-password'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
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
            ->add('codeMassar', TextType::class, [
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[A-Z]{1}[0-9]{9}$/',
                        'message' => 'Veuillez entrer un CNE valide composé d\'un caractère et de 9 chiffres.',
                    ]),
                ],
                'label' => 'Code Massar',
                'attr' => [
                    'class' => 'form-control',
                    'name' => 'codeMassar',
                ],
            ])
            ->add('dateNaissance', DateType::class, [
                'label' => 'Date de Naissance',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                    'name' => 'dateNaissance',
                ],
            ])
            ->add('lieuNaissance', TextType::class, [
                'constraints' => [
                    new Constraints(),
                    new Length([
                        'max' => 30,
                        'maxMessage' => 'Lieu de naissance ne peut pas dépasser 30 caractères.',
                    ]),
                ],
                'label' => 'Lieu de Naissance',
                'attr' => [
                    'class' => 'form-control',
                    'name' => 'lieuNaissance',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Eleve::class,
        ]);
    }
}
