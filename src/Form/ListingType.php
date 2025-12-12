<?php 
namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use App\Entity\Appartment;
use App\Entity\Listing;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ListingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('address')
            ->add('rentPrice')
            ->add('availableFrom', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
            ])
            ->add('appartment', EntityType::class, [
                'class' => Appartment::class,
                'choice_label' => 'type',
                'placeholder' => 'Select an appartment',
            ])
            ->add('imagesUpload', FileType::class, [
        'mapped' => false,
        'required' => false,
        'multiple' => true,
        'label' => 'Images',
    ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Listing::class,
        ]);
    }
}
