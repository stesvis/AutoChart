<?php

namespace AppBundle\Form;

use AppBundle\Entity\VehicleInfo;
use AppBundle\Service\VehicleService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VehicleInfoFormType extends AbstractType
{
    protected $vehicleService;

    public function __construct(VehicleService $vehicleService)
    {
        $this->vehicleService = $vehicleService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('vehicle', EntityType::class, [
                'class' => 'AppBundle\Entity\Vehicle',
                'choices' => $this->vehicleService->getMyVehicles([
                    'year' => 'DESC'
                ]),
                'choice_label' => 'name',
                'empty_data' => null,
                'placeholder' => '',
                'attr' => [
                    'class' => 'form-control ' . ($options['hideVehicle'] == true ? 'hidden' : ''),
                    'data-validation' => 'required',
                ],
                'label_attr' => [
                    'class' => 'form-control ' . ($options['hideVehicle'] == true ? 'hidden' : '')
                ],
            ])
            ->add('name', TextType::class, [
                'attr' => [
                    'data-validation' => 'required'
                ],
            ])
            ->add('value', TextType::class, [
                'attr' => [
                    'data-validation' => 'required'
                ],
            ])
            ->add('save', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary ' . ($options['hideSubmit'] == true ? 'hidden' : '')
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => VehicleInfo::class,
            'hideVehicle' => false,
            'hideSubmit' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_vehicle_info_form_type';
    }
}
