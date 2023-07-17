<?php

namespace App\Form;

use App\Entity\Evaluation;
use App\Repository\EvaluationRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChoiceEvType extends AbstractType
{private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    { 
        $builder
       
        ->add('eval', EntityType::class, [
            'class' => Evaluation::class,
            'label' => 'evaluation',
            'attr' => ['placeholder' => ''],
            'query_builder' => function (EvaluationRepository $er) {
                 
                return $er
                ->createQueryBuilder('u')
                
                ->where('u.idUser = :id')
                ->setParameter('id', $this->security->getUser())
                ->andWhere('u.enabled = 1')
            
               
               ;
            },
            'choice_label' => function (Evaluation $cr) {
                
                    return $cr->getLibelle() ;
                    
                
            },
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            
        ]);
    }
}
