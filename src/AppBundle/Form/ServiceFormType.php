<?php

namespace AppBundle\Form;

use AppBundle\Entity\Service;
use AppBundle\Includes\StatusEnums;
use AppBundle\Service\TaskService;
use AppBundle\Service\VehicleService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceFormType extends AbstractType
{
    protected $taskService;
    protected $vehicleService;

    public function __construct(TaskService $taskService, VehicleService $vehicleService)
    {
        $this->taskService = $taskService;
        $this->vehicleService = $vehicleService;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('task', EntityType::class, [
                'class' => 'AppBundle\Entity\Task',
                'choices' => $this->taskService->getMyTasks(StatusEnums::Active),
                'choice_label' => 'name',
                'empty_data' => null,
                'placeholder' => '',
                'attr' => [
                    'data-validation' => 'required'
                ],
            ])
            ->add('vehicle', EntityType::class, [
                'class' => 'AppBundle\Entity\Vehicle',
                'choices' => $this->vehicleService->getMyVehicles(StatusEnums::Active),
                'choice_label' => 'name',
                'empty_data' => null,
                'placeholder' => '',
                'attr' => [
                    'data-validation' => 'required'
                ],
            ])
            ->add('mileage')
            ->add('intervalMonths', IntegerType::class, [
                'label' => 'Remind me very N months',
            ])
            ->add('notes', TextareaType::class, [
                "attr" => [
                    "rows" => 7
                ]
            ])
            ->add('save', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Service::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'app_bundle_service_form_type';
    }


}
