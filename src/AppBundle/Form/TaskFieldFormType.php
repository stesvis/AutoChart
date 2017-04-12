<?php

namespace AppBundle\Form;

use AppBundle\Entity\TaskField;
use AppBundle\Includes\StatusEnums;
use AppBundle\Service\TaskService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskFieldFormType extends AbstractType
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'data-validation' => 'required'
                ],
            ])
//            ->add('defaultValue')
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
            ->add('save', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TaskField::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_taskfield_form_type';
    }
}
