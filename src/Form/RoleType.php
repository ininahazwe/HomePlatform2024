<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $user = $options['user'];

        $builder
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Student' => 'ROLE_CANDIDAT',
                    'Mentor' => 'ROLE_MENTOR',
                    'Admin' => 'ROLE_ADMIN',
                    'Super Admin' => 'ROLE_SUPER_ADMIN'
                ],
                //'multiple' => false,
                'mapped' => false,
                'label' => 'Rôles',
                'attr' => [
                    'class' => 'checkboxes square'
                ]
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => User::class,
        ]);
        $resolver->setRequired([
            'user',
        ]);
    }
}
