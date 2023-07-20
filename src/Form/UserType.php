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
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Doctrine\ORM\EntityManagerInterface;

class UserType extends AbstractType
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
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
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Callback([$this, 'validateEmail']),
                ],
            ])
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
                    'accept' => '.jpg,.jpeg,.png',
                    'placeholder' => 'choisir une image (JPG, PNG)',
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
    public function validateEmail($email, ExecutionContextInterface $context)
    {
        // Vérifier si l'email existe dans la table "user"
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            // L'email n'existe pas
            return;
        }

        if ($user->isEnabled()) {
            // L'email est bloqué
            $context->buildViolation('L\'email saisi est bloqué.')
                ->atPath('email')
                ->addViolation();
        }
    }
}
