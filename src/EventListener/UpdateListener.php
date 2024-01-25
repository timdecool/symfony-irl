<?php

namespace App\EventListener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsDoctrineListener(event: Events::preUpdate)]
final class UpdateListener
{
    #[AsEventListener(event: PreUpdateEventArgs::class)]
    public function preUpdate($event): void
    {
        $entity = $event->getObject();
        $entity->setUpdatedAt(new \DateTime());
    }
}