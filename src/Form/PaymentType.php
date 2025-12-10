<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Payment;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', null, [
                'label' => 'Montant (TND)',
                'attr' => ['placeholder' => 'Entrez le montant']
            ])
            ->add('paymentDate', null, [
                'widget' => 'single_text',
                'label' => 'Date du Paiement',
                'attr' => ['type' => 'date']
            ])
            ->add('paymentStatus', null, [
                'label' => 'Statut du Paiement',
                'attr' => ['placeholder' => 'Ex: paid, pending, failed']
            ])
            ->add('bookingId', EntityType::class, [
                'class' => Booking::class,
                'choice_label' => function(Booking $booking) {
                    return sprintf(
                        'Réservation #%d - %s (%s)',
                        $booking->getId(),
                        $booking->getListingId()?->getTitle() ?? 'N/A',
                        $booking->getStartDate()?->format('d/m/Y') ?? 'N/A'
                    );
                },
                'label' => 'Réservation',
                'placeholder' => 'Sélectionnez une réservation',
                'query_builder' => null
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Payment::class,
        ]);
    }
}
