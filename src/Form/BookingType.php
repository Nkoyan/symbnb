<?php

namespace App\Form;

use App\Entity\Booking;
use App\Form\DataTransformer\FrenchToDateTimeTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends ApplicationType
{
    public function __construct(private readonly FrenchToDateTimeTransformer $frenchToDateTimeTransformer)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'startDate',
                TextType::class,
                $this->getConfiguration("Date d'arrivée", 'Date à laquelle vous comptez arriver', [
                    'attr' => [
                        'autocomplete' => 'off',
                    ],
                ])
            )
            ->add(
                'endDate',
                TextType::class,
                $this->getConfiguration('Date de départ', 'La date à laquelle vous quittez les lieux', [
                    'attr' => [
                        'autocomplete' => 'off',
                    ],
                ])
            )
            ->add(
                'comment',
                TextareaType::class,
                $this->getConfiguration('', "Si vous avez un commentaire, n'hésitez pas à en faire part !", [
                    'required' => false,
                ])
            )
        ;

        $builder->get('startDate')->addModelTransformer($this->frenchToDateTimeTransformer);
        $builder->get('endDate')->addModelTransformer($this->frenchToDateTimeTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
            'validation_groups' => ['Default', 'front'],
        ]);
    }
}
