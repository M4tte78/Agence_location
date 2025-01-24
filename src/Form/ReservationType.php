<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Entity\Vehicle;
use App\Entity\AppUser;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isAdmin = $options['is_admin'] ?? false;

        $builder
            ->add('startDate', DateTimeType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('endDate', DateTimeType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ]);

        if ($isAdmin) {
            $builder
                ->add('customer', EntityType::class, [
                    'class' => AppUser::class,
                    'choice_label' => 'email',
                    'label' => 'Client',
                    'attr' => ['class' => 'form-control'],
                ])
                ->add('vehicle', EntityType::class, [
                    'class' => Vehicle::class,
                    'choice_label' => function (Vehicle $vehicle) {
                        return $vehicle->getBrand() . ' - ' . $vehicle->getModel();
                    },
                    'label' => 'Véhicule',
                    'attr' => ['class' => 'form-control'],
                ])
                ->add('totalPrice', NumberType::class, [
                    'label' => 'Prix total (€)',
                    'attr' => ['class' => 'form-control'],
                    'required' => false,
                ]);
        }
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'is_admin' => false, // Définit si le formulaire est utilisé par un administrateur
        ]);
    }
}
