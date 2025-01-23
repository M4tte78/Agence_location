<?php

namespace App\Form;

use App\Entity\AppUser;
use App\Entity\Reservation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate', null, [
                'widget' => 'single_text',
                'label' => 'Date de début',
            ])
            ->add('endDate', null, [
                'widget' => 'single_text',
                'label' => 'Date de fin',
            ])
            ->add('totalPrice')
            ->add('status')
            ->add('customer', EntityType::class, [
                'class' => AppUser::class,
                'choice_label' => 'email',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
