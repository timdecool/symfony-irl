<?php

namespace App\EventListener;

use App\Entity\Profile;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsDoctrineListener(event: Events::prePersist)]
final class InsertListener
{
    
    public function __construct(
        private Security $security
        )
        {
        }
        
        
    #[AsEventListener(event: PrePersistEventArgs::class)]
    public function prePersist($event): void
    {
        $entity = $event->getObject();

        $entity->setCreatedAt(new \DateTimeImmutable());

        if($entity instanceOf User) 
        {
            $entity->setIsActive(true);
            $profile = new Profile();
            $entity->setProfile($profile);
        }
    }
}
