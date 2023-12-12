<?php

namespace App\Form;

use App\Entity\File;
use App\Entity\Messages;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessagesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['user'];
        $builder
            ->add('title', TextType::class, [
                'required' => false,
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('file', EntityType::class, [
                'class' => File::class,
                'label' => 'Avatar',
                'multiple' => true,
                'mapped' => false,
                'required' => false
            ])
            ->add('message', TextareaType::class, [
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('recipient', EntityType::class, [
                "class" => User::class,
                'required' => false,
                "choice_label" => "fullname",
                "attr" => [
                    "class" => "form-control"
                ],
                'query_builder' => function (EntityRepository $er) use ($user){
                $query = $er->createQueryBuilder('u');
                if($user->isSuperAdmin()){
                    $query->select('u')
                        ->andWhere('u.id != :id')
                        ->setParameter('id', $user->getId())
                    ;
                } elseif ($user->isAdmin()){
                    $query->select('u')
                        ->andWhere('u.id != :id')
                        ->setParameter('id', $user->getId())
                    ;
                } elseif ($user->isMentor()){
                    $query->select('u')
                        ->andWhere('u.id != :id')
                        ->setParameter('id', $user->getId())
                    ;
                } elseif ($user->isCandidat()){
                    $groupIds = [];
                    foreach ($user->getGroups() as $group){
                        $groupIds[] = $group->getId();
                    }
                    $query->select('u')
                        ->leftJoin('u.groups', 'groups')
                        ->andWhere($query->expr()->in('groups.id', $groupIds))
                        ->andWhere('u.id != :id')
                        ->setParameter('id', $user->getId())
                    ;

                } else {
                    return null;
                }

                return $query;
                }
            ])
            ->add('parentid', HiddenType::class, [
                'mapped' => false
            ])
            ->add('envoyer', SubmitType::class, [
                "attr" => [
                    "class" => "btn btn-primary"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Messages::class,
            'csrf_protection' => false,
        ]);
        $resolver->setRequired([
            'user',
        ]);
    }
}
