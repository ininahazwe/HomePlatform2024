<?php

namespace App\Form;

use App\Entity\Desinscription;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DesinscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('motif', ChoiceType::class, [
                'choices' => Desinscription::getMotifList(),
                'label'=> 'why you wish to delete your account',
                'expanded' => true,
                /*'attr' => [
                    'class' => 'radio-box'
                ]*/
            ])
            ->add('autreMotif', TextType::class, [
                    'label' => 'Autre',
                    'required' => false,
                    ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Desinscription::class,
        ]);
    }
}
