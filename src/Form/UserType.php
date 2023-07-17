<?php

namespace App\Form;

use App\Entity\Departement;
use App\Entity\User;
use App\Repository\DepartementRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', TextType::class,[
                'label' => 'Identifiant',
                'attr' => [
                    'class' => 'form-control',
                ],
                'invalid_message_parameters' => [
                    '%class%' => 'is-invalid',
                ],
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
            ->add('email')
            ->add('roles', ChoiceType::class, [
                'label' => 'Role',
                'attr' => [
                    'class' => 'form-control',
                ],
                'invalid_message_parameters' => [
                    '%class%' => 'is-invalid',
                ],
                'choices' => [
                    'Utilisateur' => 'ROLE_Utilisateur',
                    'Gestionnaire' => 'ROLE_GESTIONNAIRE',
                ],
                'expanded' => false,
                'multiple' => true,
            ])
            ->add('password', PasswordType::class, [

                'required' => true,
                

            ])
            ->add('image', FileType::class, [
                'required' => false,
                'mapped' => false,
                'label' => 'Image (JPG, PNG)',
                
                'attr' => [
                    'accept' => '.jpg,.jpeg,.png'
                ]
            ])


            ->add('idDepartement', EntityType::class, [
                'class' => Departement::class,
                'label' => 'Departement',
                'attr' => [
                    'class' => 'form-control',
                ],
                'query_builder' => function (DepartementRepository $er) {
                    return $er
                        ->createQueryBuilder('d')
                        ->where('d.enabled = 1');
                },
                'choice_label' => function (Departement $departement) {

                    return $departement->getLibelle();
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
