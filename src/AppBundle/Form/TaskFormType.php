<?php

namespace AppBundle\Form;

use AppBundle\Entity\Task;
use AppBundle\Includes\StatusEnums;
use AppBundle\Service\CategoryService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskFormType extends AbstractType
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'data-validation' => 'required'
                ]
            ])
            ->add('category', EntityType::class, [
                'class' => 'AppBundle\Entity\Category',
                'choices' => $this->categoryService->getMyCategories(StatusEnums::Active),
                'choice_label' => 'name',
                'empty_data' => null,
                'placeholder' => '',
                'attr' => [
                    'data-validation' => 'required'
                ]
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'rows' => 2
                ],
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary ' . ($options['hideSubmit'] == true ? 'hidden' : '')
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
            'hideSubmit' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_task_form_type';
    }
}
