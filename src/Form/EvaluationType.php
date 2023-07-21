<?php

namespace App\Form;

use App\Entity\Departement;
use App\Entity\Evaluation;
use App\Repository\DepartementRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class EvaluationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle')
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
            'data_class' => Evaluation::class,
        ]);
    }
}
