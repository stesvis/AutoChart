<?php

namespace AppBundle\Form;

use AppBundle\Entity\Category;
use AppBundle\Includes\StatusEnums;
use AppBundle\Service\CategoryService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryFormType extends AbstractType
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description', TextareaType::class, [
                'attr' => array('rows' => 2)
            ])
            ->add('parentCategory', EntityType::class, [
                'class' => 'AppBundle\Entity\Category',
                'choices' => $this->categoryService->getMyCategories(StatusEnums::Active),
                'choice_label' => 'name',
                'empty_data' => null,
                'placeholder' => '',
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
            'data_class' => Category::class,
            'hideSubmit' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_category_form_type';
    }
}
