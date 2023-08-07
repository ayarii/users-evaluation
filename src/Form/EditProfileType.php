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
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class EditProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('id', null, [
            'label' => 'Identifiant',
            'disabled' => true,
        ])
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
            ->add('email',EmailType::class ,  [
                'attr' => [
                    'class' => 'form-control',
                ],
                'invalid_message_parameters' => [
                    '%class%' => 'is-invalid',
                ],
            ])
           
            ->add('image', FileType::class, [
                'required' => false, 
                'mapped' => false, 
                'label' => 'Image (JPG, PNG)', 
                'attr' => [
                    'accept' => '.jpg,.jpeg,.png' ,
                    'placeholder' => 'choisir une image (JPG, PNG)',
                ]
            ])
          
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
