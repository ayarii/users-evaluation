<?php

namespace App\Form;

use App\Entity\Critere;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CritereType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', TextType::class,  [
                'attr' => [
                    'class' => 'form-control',
                ],
              
            ])
            ->add('ponderation', TextType::class,  [
                'attr' => [
                    'class' => 'form-control',
                ],
              
            ])
            ->add('description',CKEditorType::class, array(
                'config' => array(
                    'uiColor' => '#ffffff',
                    //...
                ),
                'attr' => [
                    'class' => 'form-control',
                ],
            ))

           
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Critere::class,
        ]);
    }
}
