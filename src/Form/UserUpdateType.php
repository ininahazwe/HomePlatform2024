<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Group;
use App\Entity\Profile;
use App\Entity\Project;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'required' => true,
                ]
            ])
            ->add('isVerified')
            ->add('nom', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'required' => true,
                ],
                'label' => 'Lastname'
            ])
            ->add('prenom', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'required' => true,
                ],
                'label' => 'Firstname'
            ])
            ->add('civilite',  ChoiceType::class, [
                'choices' => User::getGenreName(),
                'attr' => [
                    'class' => 'form-control',
                    'required' => true,
                ],
                'label' => 'Gender'
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Candidate' => 'ROLE_CANDIDAT',
                    'Mentor' => 'ROLE_MENTOR',
                ],
                'expanded' => true,
                'multiple' => true,
                'label' => 'Roles'
            ])
            ->add('projects', EntityType::class, [
                'class' => Project::class,
                'multiple' => true,
                'choice_label' => 'nom',
                'required' => false,
                'label' => 'Projects',
                'query_builder' => function (EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.nom', 'ASC');
                },
                'by_reference' => true,
                'attr' => [
                    'class' => 'select-tags'
                ]
            ])
            ->add('groups', EntityType::class, [
                'class' => Group::class,
                'multiple' => true,
                'choice_label' => 'nom',
                'required' => false,
                'label' => 'Group',
                'query_builder' => function (EntityRepository $er){
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.nom', 'ASC');
                },
                'by_reference' => true,
                'attr' => [
                    'class' => 'select-tags'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
