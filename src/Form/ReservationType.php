<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Entity\Vehicle;
use App\Entity\AppUser;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date de début',
            ])
            ->add('endDate', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date de fin',
            ])
            ->add('totalPrice', MoneyType::class, [
                'currency' => 'EUR',
                'label' => 'Prix total',
            ])
            ->add('status', TextType::class, [
                'label' => 'Statut',
            ])
            ->add('customer', EntityType::class, [
                'class' => AppUser::class,
                'choice_label' => 'email',
                'label' => 'Client',
            ])
            ->add('vehicle', EntityType::class, [
                'class' => Vehicle::class,
                'choice_label' => 'model',
                'label' => 'Véhicule',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}