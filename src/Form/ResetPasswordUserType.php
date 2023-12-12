<?php

namespace App\Form;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ResetPasswordUserType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('oldPassword', PasswordType::class, array(
                'label' => 'Votre Ancien mot de passe',
                'mapped' => false
            ))

            ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'mapped' => false,
                    //'attr' => ['autocomplete' => 'new-password'],
//                    'constraints' => [
//                            new NotBlank([
//                                    'message' => 'Veuillez saisir un mot de passe',
//                            ]),
//                            new Length([
//                                    'min' => 6,
//                                    'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
//                                // max length allowed by Symfony for security reasons
//                                    'max' => 4096,
//                            ]),
//                           /* new Regex([
//                                    'pattern'=>"/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/",
//                                    'message'=>" Password must be at least 6 characters: 1 uppercase, 1 lowercase, numbers, or symbols."
//                            ])*/
//                    ],
//                    'invalid_message' => 'The password fields must match.',
                    'options' => ['attr' => ['class' => 'password-field']],
                    'required' => true,
                    'first_options'  => ['label' => 'Mot de passe'],
                    'second_options' => ['label' => 'Confirmer le mot de passe'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
                'data_class' => User::class,
                'csrf_protection' => false,
        ]);
    }
}
