<?php

namespace App\Form;

use App\Entity\Edition;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;

class EditionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('city', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('year', TextType::class, [
                    'attr' => [
                            'class' => 'form-control'
                    ]
            ])
            ->add('intro', CKEditorType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('ordre')
            ->add('status', ChoiceType::class, [
                'label' => 'Status',
                'choices' => [
                    'Published' => true,
                    'Unpublished' => false
                ]

            ])
            ->add('description', CKEditorType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('video', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => false,
            ])
            ->add('avatar', DropzoneType::class,[
                'label' => 'Avatar',
                'multiple' => true,
                'mapped' => false,
                'required' => false
            ])
            ->add('illustration', DropzoneType::class,[
                'label' => 'Illustrations',
                'multiple' => true,
                'mapped' => false,
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Edition::class,
        ]);
    }
}
