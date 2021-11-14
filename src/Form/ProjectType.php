<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Project;
use App\Entity\Tag;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Name',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('images', DropzoneType::class, [
                'attr' => [
                    'placeholder' => 'Choisir une image d\'illustration',
                    'data-controller' => 'mydropzone'
                ],
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => false,
            ])

            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'multiple' => true,
                'choice_label' => 'nom',
                'query_builder' => function (EntityRepository $er){
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.nom', 'ASC');
                },
                'by_reference' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'multiple' => true,
                'choice_label' => 'nom',
                'required' => false,
                'query_builder' => function (EntityRepository $er){
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.nom', 'ASC');
                },
                'label' => 'Tags',
                'by_reference' => false,
                'attr' => [
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
    }
}
