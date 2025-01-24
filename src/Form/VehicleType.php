<?php

namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Vehicle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class VehicleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ pour la marque
            ->add('brand', TextType::class, [
                'label' => 'Marque',
                'attr' => ['placeholder' => 'Entrez la marque du véhicule'],
            ])

            // Champ pour le modèle
            ->add('model', TextType::class, [
                'label' => 'Modèle',
                'attr' => ['placeholder' => 'Entrez le modèle du véhicule'],
            ])

            // Champ pour l'immatriculation
            ->add('registration', TextType::class, [
                'label' => 'Immatriculation',
                'attr' => ['placeholder' => 'Entrez le numéro d\'immatriculation'],
            ])

            // Champ pour le type de véhicule
            ->add('type', TextType::class, [
                'label' => 'Type de véhicule',
                'attr' => ['placeholder' => 'Entrez le type (ex : SUV, berline...)'],
            ])

            // Champ pour le prix par jour
            ->add('pricePerDay', NumberType::class, [
                'label' => 'Prix par jour (€)',
                'attr' => ['placeholder' => 'Entrez le tarif journalier en euros'],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le prix par jour est obligatoire.',
                    ]),
                    new Assert\Range([
                        'min' => 20,
                        'max' => 300,
                        'notInRangeMessage' => 'Le prix par jour doit être compris entre {{ min }} € et {{ max }} €.',
                    ]),
                ],
            ])
            

            // Champ pour la disponibilité
            ->add('availabilityStatus', CheckboxType::class, [
                'label' => 'Disponible',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input',
                    'disabled' => $options['disable_availability'] ?? false,
                ],
            ])

            // Champ pour l'image
            ->add('image', FileType::class, [
                'label' => 'Image du véhicule (JPG ou PNG)',
                'mapped' => false, // Non mappé à l'entité, car géré manuellement
                'required' => false,
                'attr' => ['accept' => 'image/*'], // Limite les formats acceptés
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vehicle::class, // Associe ce formulaire à l'entité Vehicle
        ]);
    }
}
