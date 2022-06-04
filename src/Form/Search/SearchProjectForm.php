<?php

namespace App\Form\Search;

use App\Data\SearchDataProject;
use App\Entity\Categorie;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchProjectForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('q', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Keywords / Project Title'
                ]
            ])
            ->add('categories', EntityType::class, [
                'class' => Categorie::class,
                'multiple' => true,
                'label' => false,
                'expanded' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'by SDG'
                ],
                'query_builder' => function($repository) {
                    $ids = $repository->findAll();
                    $query = $repository->createQueryBuilder('c')
                        ->select('c')
                        ->andWhere('c.id IN (:ids)')
                        ->setParameter('ids', $ids)
                    ;
                    return $query;
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchDataProject::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }

    public function getName(): string
    {
        return 'search_project';
    }
}
