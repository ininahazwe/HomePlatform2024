<?php

namespace App\Form;

use App\Entity\Dictionnaire;
use App\Entity\Profile;
use App\Entity\Tag;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('school', EntityType::class, [
                'required'  => true,
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'School Name',
                'expanded' => false,
                'class' => 'App\Entity\Dictionnaire',
                'query_builder' => function($repository) {
                    $query = $repository->createQueryBuilder('d')
                        ->select('d')
                        ->where('d.type = :type')
                        ->setParameter('type', Dictionnaire::NAME_SCHOOL);

                    return $query;
                }
            ])

            ->add('level', EntityType::class, [
                'required'  => true,
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Highest education level',
                'expanded' => false,
                'class' => 'App\Entity\Dictionnaire',
                'query_builder' => function($repository) {
                    $query = $repository->createQueryBuilder('d')
                        ->select('d')
                        ->where('d.type = :type')
                        ->setParameter('type', Dictionnaire::EDUCATION_LEVEL);

                    return $query;
                }
            ])
            ->add('photo', DropzoneType::class,[
                'label' => 'My picture',
                'multiple' => true,
                'mapped' => false,
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => false,
            ])
            ->add('facebook', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => false,
            ])
            ->add('instagram', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => false,
            ])
            ->add('lindedin', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => false,
            ])
            ->add('twitter', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => false,
            ])
            /*->add('skills', EntityType::class, [
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
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Profile::class,
        ]);
    }
}
