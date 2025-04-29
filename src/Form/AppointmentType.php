<?php

namespace App\Form;

use App\Entity\Appointment;
use App\Entity\ProductUsage;
use App\Entity\Service;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AppointmentType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', null, [
                'widget' => 'single_text',
                'attr' => [
                    'id' => 'appointment_date',
                    'min' => (new \DateTime())->format('Y-m-d\T09:00'),
                    'max' => (new \DateTime('+1 month'))->format('Y-m-d\T18:00'),
                    'step' => '1800',
                    'class' => 'form-control'
                ]
            ])
            ->add('service', EntityType::class, [
                'class' => Service::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisissez un service'
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'fullName',
                'required' => false,
                'placeholder' => 'Sélectionner un client existant',
            ])
            ->add('newUserFullName', TextType::class, [
                'required' => false,
                'mapped' => false,
                'label' => 'Nouveau client',
                'attr' => [
                    'placeholder' => 'Nom et prénom du client'
                ]
            ])
            ->add('newUserEmail', TextType::class, [
                'required' => false,
                'mapped' => false,
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'Adresse email'
                ]
            ])
            ->add('newUserPhone', TextType::class, [
                'required' => false,
                'mapped' => false,
                'label' => 'Téléphone',
                'attr' => [
                    'placeholder' => 'Numéro de téléphone'
                ]
            ])

        ;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appointment::class,
        ]);
    }
}
