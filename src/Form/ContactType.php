<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',TextType::class, [
                'attr' => [
                    'placeholder' => 'Please enter your name'
                ],
                'required' => true
            ])
            ->add('subject',TextType::class, [
                'attr' => [
                    'placeholder' => 'Please enter your subject'
                ],
                'required' => true
            ])
            ->add('email',EmailType::class, [
                'attr' => [
                    'placeholder' => 'Please enter your email'
                ],
                'required' => true
            ])
            ->add('message', TextareaType::class, [
                'required' => true,
                'attr' => [
                    'rows' => 6,
                    'placeholder' => 'Write your message',
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => "I agree to the terms of use",
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You must agree to our terms.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        ]);
    }
}