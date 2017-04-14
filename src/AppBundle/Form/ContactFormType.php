<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'attr' => array('placeholder' => 'Your name'),
                'attr' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Please provide your Name',
                ]
            ))
            ->add('subject', TextType::class, array(
                'attr' => array('placeholder' => 'Subject'),
                'attr' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Please enter a Subject',
                ],
            ))
            ->add('email', EmailType::class, array(
                'attr' => array('placeholder' => 'Your email address'),
                'constraints' => array(
                    new NotBlank(array("message" => "Please provide a valid email")),
                    new Email(array("message" => "Your email doesn't seems to be valid")),
                ),
                'attr' => [
                    'data-validation' => 'email',
                    'data-validation-error-msg' => 'Please provide your Email address',
                ]
            ))
            ->add('message', TextareaType::class, array(
                'attr' => array('placeholder' => 'Your message here'),
                'constraints' => array(
                    new NotBlank(array("message" => "Please provide a message here")),
                ),
                'attr' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Please type a Message',
                ]
            ))
            ->add('send', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ]);
//            ->add('cancel', SubmitType::class, [
//                'attr' => [
//                    'class' => 'btn btn-default'
//                ]
//            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'error_bubbling' => true
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_contact_form_type';
    }
}
