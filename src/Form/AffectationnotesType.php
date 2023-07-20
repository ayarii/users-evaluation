<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Affectationnotes;
use App\Entity\Critere;
use App\Entity\Evaluation;
use App\Repository\CritereRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
class AffectationnotesType extends AbstractType
{ private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder,array $options): void
    {  
        $builder
        ->add('user', EntityType::class, [
            'class' => User::class,
            'label' => 'Utilisateur',
            'attr' => ['placeholder' => ''],
            'query_builder' => function (UserRepository $er) {
                return $er
                ->createQueryBuilder('u')
            
                ->where('u.roles LIKE :roles')
                ->setParameter('roles', '%"'."ROLE_Utilisateur".'"%')
            
               
               ;
            },
            'choice_label' => function (User $user) {
                
                    return $user->getNom() . ' ' . $user->getPrenom();
                    
                
            },
        ])
            
            ->add('critere', EntityType::class, [
                'class' => Critere::class,
                'label' => 'critere',
                'attr' => ['placeholder' => ''],
                'query_builder' => function (CritereRepository $er) {
                     
                    return $er
                    ->createQueryBuilder('u')
                    ->join(Evaluation::class,'e')
                    ->where('e.id=u.idEvaluation')
                    ->andWhere('e.idUser = :id')
                    ->setParameter('id', $this->security->getUser())
                    ->andWhere('u.enabled = 1')
                
                   
                   ;
                },
                'choice_label' => function (Critere $cr) {
                    
                        return $cr->getLibelle() ;
                        
                    
                },
            ])
            ->add('note')
           ;
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Affectationnotes::class,
        ]);
    }
}
