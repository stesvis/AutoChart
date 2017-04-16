<?php

namespace AppBundle\Form;

use AppBundle\Entity\Vehicle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VehicleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('year', IntegerType::class, [
                'attr' => [
                    'data-validation' => 'number'
                ],
            ])
            ->add('make', TextType::class, [
                'attr' => [
                    'data-validation' => 'required'
                ],
            ])
            ->add('model', TextType::class, [
                'attr' => [
                    'data-validation' => 'required'
                ],
            ]);

        if ($options['popup'] === false) {

            $builder
                ->add('trim')
                ->add('color')
                ->add('transmissionType', ChoiceType::class, [
                    'choices' => [
                        'Auto' => 'Auto',
                        'Manual' => 'Manual'
                    ],
                    'empty_data' => null,
                    'placeholder' => '',
                ])
                ->add('fuelType', ChoiceType::class, [
                    'choices' => [
                        'Gas' => 'Gas',
                        'Diesel' => 'Diesel',
                        'Propane' => 'Propane',
                    ],
                    'empty_data' => null,
                    'placeholder' => '',
                ])
                ->add('engineSize')
                ->add('passengers')
                ->add('mileageType', ChoiceType::class, [
                    'choices' => [
                        'Km' => 'Km',
                        'Miles' => 'Miles',
                    ],
                    'empty_data' => null,
                    'placeholder' => '',
                ])
                ->add('mileage', IntegerType::class, [
                    'label' => 'Current Mileage',
                ])
                ->add('annualMileage', IntegerType::class, [
                    'label' => 'Average Annual Mileage',
                ])
                ->add('description', TextareaType::class, [
                    'attr' => array('rows' => 2),
                ])
                ->add('price', IntegerType::class)
                ->add('purchasedAt', DateType::class, [
                    'widget' => 'single_text',
                    'html5' => false,
                    'attr' => ['class' => 'js-datepicker'],
                ])
                ->add('save', SubmitType::class, [
                    'attr' => [
                        'class' => 'btn btn-primary',
//                    'novalidate' => 'novalidate',
                    ]
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Vehicle::class,
            'popup' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_vehicle_form_type';
    }
}
