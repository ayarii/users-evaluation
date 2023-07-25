<?php

namespace App\Form;

use App\Entity\AffectationBadge;
use App\Entity\Badge;
use App\Repository\BadgeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AffectationBadgeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateaffectation')
            ->add('idbadge', EntityType::class, [
                'class' => Badge::class,
                'label' => 'Departement',
                'attr' => [
                    'class' => 'form-control',
                ],
                'query_builder' => function (BadgeRepository $er) {
                    return $er
                        ->createQueryBuilder('b')
                        ;
                },
                'choice_label' => function (Badge $badge) {

                    return $badge->getLibelle();
                },
            ])
            ->add('iduser')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AffectationBadge::class,
        ]);
    }
}
