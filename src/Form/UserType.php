<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom <span class="required-star">*</span>',
                'label_html' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez votre nom',
                    'style' => 'border-color: black; border-width: 2px',
                ],
            ])
            ->add('surname', TextType::class, [
                'label' => 'Prénom <span class="required-star">*</span>',
                'label_html' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez votre prénom',
                    'style' => 'border-color: black; border-width: 2px',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email <span class="required-star">*</span>',
                'label_html' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez votre email',
                    'style' => 'border-color: black; border-width: 2px',
                ],
            ])
            ->add('phone', TelType::class, [
                'label' => 'Numéro de téléphone <span class="required-star">*</span>',
                'label_html' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez votre numéro',
                    'style' => 'border-color: black; border-width: 2px',
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => [
                    'label' => 'Mot de passe <span class="required-star">*</span>',
                    'label_html' => true,
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Créez un mot de passe',
                        'style' => 'border-color: black; border-width: 2px',
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe <span class="required-star">*</span>',
                    'label_html' => true,
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Confirmez votre mot de passe',
                        'style' => 'border-color: black; border-width: 2px',
                    ],
                ],
                'invalid_message' => 'Les mots de passe doivent correspondre.',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'S\'inscrire',
                'attr' => [
                    'class' => 'btn btn-danger btn-lg mx-auto d-block',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
