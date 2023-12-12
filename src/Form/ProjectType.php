<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Project;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];

        $builder
            ->add('nom', TextType::class, [
                'label' => 'Title',
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('intro', CKEditorType::class, [
                'label' => 'Introduction',
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('description', CKEditorType::class, [
                'required' => true,
                'label' => 'Content',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('statut', ChoiceType::class, [
                    'choices' => Project::getStatusName()
            ])
            ->add('video', TextType::class, [
                'label' => 'Video link',
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('images', DropzoneType::class, [
                'attr' => [
                    'placeholder' => 'Choose gallery images',
                    'data-controller' => 'mydropzone'
                ],
                'multiple' => true,
                'mapped' => false,
                'required' => false,
            ])
            ->add('avatar', DropzoneType::class, [
                'attr' => [
                    'placeholder' => 'Choose the image to illustrate the project',
                    'data-controller' => 'mydropzone'
                ],
                'label' => 'Avatar',
                'multiple' => true,
                'mapped' => false,
                'required' => false,
            ])

            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'multiple' => true,
                'choice_label' => 'nom',
                'required' => true,
                'label' => 'Categories',
                'query_builder' => function (EntityRepository $er){
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.nom', 'ASC');
                },
                'by_reference' => false,
                'attr' => [
                    'class' => 'select-tags'
                ]
            ])
            ->add('auteur', EntityType::class, [
                'class' => User::class,
                'multiple' => true,
                'compound' => false,
                'choice_label' => function (User $user) {
                    return $user->getFullname();
                },
                'required' => true,
                'label' => 'Contributors',
                'query_builder' => function ($repository) use ($user) {
                    $ids = $repository->getGroupMembersByUser($user);
                    if (!$user->isCandidat()) {
                        return $repository->createQueryBuilder('u')
                            ->select('u')
                            ->orderBy('u.email', 'ASC');
                    } else {
                        return $repository->createQueryBuilder('u')
                            ->select('u')
                            ->orderBy('u.email', 'ASC')
                            ->andWhere('u.id in (:ids)')
                            ->setParameter('ids', $ids);
                    }
                },
                'by_reference' => false,
                'attr' => [
                    'class' => 'select-tags'
                ]
            ])
            ->add('editor', EntityType::class, [
                'class' => User::class,
                'multiple' => false,
                'compound' => false,
                'choice_label' => function (User $user) {
                    return $user->getFullname();
                },
                'required' => false,
                'label' => 'Editor',
                'query_builder' => function ($repository) use ($user) {
                    $ids = $repository->getGroupMembersByUser($user);
                    if (!$user->isCandidat()) {
                        return $repository->createQueryBuilder('u')
                            ->select('u')
                            ->orderBy('u.email', 'ASC');
                            } else {
                        return $repository->createQueryBuilder('u')
                            ->select('u')
                            ->orderBy('u.email', 'ASC')
                            ->andWhere('u.id in (:ids)')
                            ->setParameter('ids', $ids);
                    }
                },
                'by_reference' => true,
                'attr' => [
                    'class' => 'select-tags'
                ]
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'multiple' => true,
                'required' => false,
                'choice_label' => 'nom',

                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.nom', 'ASC');
                },
                'label' => 'Tags',
                'by_reference' => false,
                'attr' => [
                    //'class' => 'form-control js-example-tokenizer'
                    'class' => 'select-tags'

                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);

        $resolver->setRequired([
            'user'
        ]);
    }
}
