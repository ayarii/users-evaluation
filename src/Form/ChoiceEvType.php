<?php

namespace App\Form;

use App\Entity\Evaluation;
use App\Repository\EvaluationRepository;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChoiceEvType extends AbstractType
{private Security $security;
    private UserRepository $ur;

    public function __construct(Security $security,UserRepository $ur)
    {
        $this->security = $security;
        $this->ur = $ur;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    { 
        $builder
       
        ->add('eval', EntityType::class, [
            'class' => Evaluation::class,
            'label' => 'evaluation',
            'attr' => ['placeholder' => ''],
            'query_builder' => function (EvaluationRepository $er) {
                 $admin=$this->ur->createQueryBuilder('u')

       
                 ->where('u.roles LIKE :roles')
                 ->setParameter('roles', '%"'."ROLE_ADMIN".'"%')
                 ->getQuery()
                 ->getResult();
                
                 return $er
                ->createQueryBuilder('u')
                
                ->where('u.idUser = :id OR u.idUser = :adminId')
                ->setParameter('id', $this->security->getUser())
               
                 ->setParameter('adminId', $admin[0])
               
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
