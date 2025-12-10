<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bundle\SecurityBundle\Security;

class UserType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez votre prénom'
                ]
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez votre nom'
                ]
            ])
            ->add('phoneNumber', TextType::class, [
                'label' => 'Numéro de Téléphone',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez votre numéro de téléphone'
                ]
            ])
            ->add('username', TextType::class, [
                'label' => 'Nom d\'utilisateur',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez votre nom d\'utilisateur'
                ]
            ])
            ->add('email', TextType::class, [
                'label' => 'Email',
                'attr' => ['placeholder' => 'Entrez votre adresse email', 'class' => 'form-control'],
            ]);
        
        // If user is admin, allow them to assign roles
        if ($this->security->isGranted('ROLE_ADMIN')) {
            $builder->add('roles', ChoiceType::class, [
                'label' => 'Rôles',
                'choices' => [
                    'Utilisateur Normal' => 'ROLE_USER',
                    'Propriétaire/Bailleur' => 'ROLE_OWNER',
                    'Admin' => 'ROLE_ADMIN',
                ],
                'expanded' => true,
                'multiple' => true,
                'help' => 'Sélectionnez un ou plusieurs rôles',
            ]);
        } else {
            // For regular users during registration, show role selection
            $builder->add('roles', ChoiceType::class, [
                'label' => 'Je suis...',
                'choices' => [
                    'Un locataire (cherche un appartement)' => 'ROLE_USER',
                    'Un propriétaire (loue un appartement)' => 'ROLE_OWNER',
                ],
                'expanded' => true,
                'multiple' => true,
                'help' => 'Choisissez votre type de compte',
            ]);
        }
        
        $builder->add('password', PasswordType::class, [
            'label' => 'Mot de passe',
            'attr' => ['placeholder' => 'Entrez votre mot de passe', 'class' => 'form-control'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
