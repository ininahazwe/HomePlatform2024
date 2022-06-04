<?php

namespace App\Form;

use App\Entity\Email;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Code'
            ])
            ->add('sujet', TextType::class, [
                'label' => 'Subject',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('contenu', CKEditorType::class, [
                'label'=> 'Content',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Email::class,
        ]);
    }
}
