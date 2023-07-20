<?php

namespace App\Form;

use App\Entity\Badge;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

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
            ->add('description',CKEditorType::class, array(
                'config' => array(
                    'uiColor' => '#ffffff',

                    //...
                ),
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Description du badge...'
                ],
                'invalid_message_parameters' => [
                    '%class%' => 'is-invalid',
                ],

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
