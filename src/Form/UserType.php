<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('id')
            ->add('nom', TextType::class,  [
                'attr' => [
                    'class' => 'form-control',
                ],
                'invalid_message_parameters' => [
                    '%class%' => 'is-invalid',
                ],
            ])
            ->add('prenom', TextType::class,  [
                'attr' => [
                    'class' => 'form-control',
                ],
                'invalid_message_parameters' => [
                    '%class%' => 'is-invalid',
                ],
            ])
            ->add('email')
            ->add('roles', ChoiceType::class, [
                'label' => 'Role',
                'choices' => [
                    'Utilisateur' => 'ROLE_Utilisateur',
                    'Administrateur' => 'ROLE_ADMIN',
                    'Gestionnaire' => 'ROLE_GESTIONNAIRE',
                ],
                'expanded' => false,
                'multiple' => true,
            ])
            ->add('password' ,PasswordType::class,[

                'required' => true,
                'constraints' => [new Regex([
                    'pattern' => '/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/',
                    'message' => 'Votre mot de passe doit comporter au moins 8 caractères et contenir au moins une lettre et un numéro!',
                ]),
                new NotBlank([
                    'message' => 'Mot de passe Obligatoire!',
                ])
            ],

            ])
            ->add('image', FileType::class, [
                'required' => false, 
                'mapped' => false, 
                'label' => 'Image (JPG, PNG)', 
                'attr' => [
                    'accept' => '.jpg,.jpeg,.png' 
                ]
            ])
          /*  ->add('createdAt',  DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control datepicker'],
                'invalid_message_parameters' => [
                    '%class%' => 'is-invalid',
                ],
            ])
            ->add('updatedAt',  DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control datepicker'],
                'invalid_message_parameters' => [
                    '%class%' => 'is-invalid',
                ],
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
