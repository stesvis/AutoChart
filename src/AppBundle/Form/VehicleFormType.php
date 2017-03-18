<?php

namespace AppBundle\Form;

use AppBundle\Entity\Vehicle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VehicleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('year')
            ->add('make')
            ->add('model')
            ->add('trim')
            ->add('color')
            ->add('transmissionType', ChoiceType::class, [
                'choices' => [
                    '' => '',
                    'Auto' => 'Auto',
                    'Manual' => 'Manual'
                ]
            ])
            ->add('fuelType', ChoiceType::class, [
                'choices' => [
                    '' => '',
                    'Gas' => 'Gas',
                    'Diesel' => 'Diesel',
                    'Propane' => 'Propane',
                ]
            ])
            ->add('engineSize')
            ->add('passengers')
            ->add('mileage')
            ->add('description', TextareaType::class, [
                'attr' => array('rows' => 2),
            ])
            ->add('price')
            ->add('purchasedAt', DateType::class)
            ->add('save', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Vehicle::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_vehicle_form_type';
    }
}
