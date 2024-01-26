<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Group;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function __construct(
        private Security $security
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('relatedGroup', EntityType::class, [
                'class' => Group::class,
                'choice_label' => 'name',
                'choices' => $this->security->getUser()->getGroups(),
            ])
            ->add('name')
            ->add('beginAt')
            ->add('endAt')
            ->add('place')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
