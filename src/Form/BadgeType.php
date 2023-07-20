<?php

namespace App\Form;

use App\Entity\Badge;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class BadgeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle',TextType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Libelle du badge...'
                ],
                'invalid_message_parameters' => [
                    '%class%' => 'is-invalid',
                ],
            ]
            )
            ->add('action',TextType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Action à faire poour obtenir le badge...'
                ],
                'invalid_message_parameters' => [
                    '%class%' => 'is-invalid',
                ],
            ]
            )
            ->add('image', FileType::class, [
                'required'=>true,
                'mapped' => true,
                'label' => 'Image (JPG, PNG)',
                
                'attr' => [
                    'accept' => '.jpg,.jpeg,.png',
                    'placeholder' => 'choisir une image (JPG, PNG)',
                ]
            ])
            ->add('description',CKEditorType::class, array(
                'config' => array(
                    'uiColor' => '#ffffff',
                    //...
                ),
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Badge::class,
        ]);
    }
}
