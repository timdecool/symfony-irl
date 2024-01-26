<?php

namespace App\Form;

use App\Entity\Availability;
use App\Entity\Group;
use App\Entity\User;
use App\Repository\GroupRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AvailabilityType extends AbstractType
{

    public function __construct(
        private Security $security
    )
    {}


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('beginAt')
            ->add('endAt')
            ->add('relatedGroup', EntityType::class, [
                'class' => Group::class,
                // 'query_builder' => function(EntityRepository $er) : QueryBuilder {
                //     return $er->createQueryBuilder('g')
                //         ->leftJoin('g.users', 'users')
                //         ->andWhere('users.id = :val')
                //         ->setParameter('val', $this->security->getUser())
                //         ;
                // },
                'choices' => $this->security->getUser()->getGroups(),
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Availability::class,
        ]);
    }
}
