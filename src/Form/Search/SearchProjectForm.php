<?php

namespace App\Form\Search;

use App\Data\SearchDataProject;
use App\Entity\Categorie;
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
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'multiple' => true,
                'label' => false,
                'required' => false,
                'expanded' => true,
                'query_builder' => function($repository) {
                    $ids = $repository->getCategoriesWithProjects();
                    return $repository->createQueryBuilder('c')
                        ->select('c')
                        ->orderBy('c.nom', 'ASC')
                        ->andWhere('c.id in (:ids)')
                        ->setParameter('ids', $ids);
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
        return 'search_projects';
    }
}
