<?php

namespace App\Form;

use App\Entity\About;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;

class AboutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('intro', CKEditorType::class)
            ->add('titre2', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('description', CKEditorType::class)
            ->add('description2', CKEditorType::class)
            ->add('video', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('accroche', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('images', DropzoneType::class,[
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => About::class,
        ]);
    }
}
