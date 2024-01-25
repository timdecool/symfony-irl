<?php

namespace App\EventListener;

use App\Entity\Event;
use App\Entity\Group;
use App\Entity\Profile;
use App\Entity\User;
use App\Repository\PictureRepository;
use App\Repository\ProfileRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsDoctrineListener(event: Events::prePersist)]
final class InsertListener
{
    
    public function __construct(
        private Security $security,
        private PictureRepository $pictureRepository
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
            $entity->setRoles(["ROLE_USER"]);
            $profile = new Profile();
            $profile->setPicture($this->pictureRepository->findOneBy(['id' => 1]));
            $entity->setProfile($profile);
        }

        if($entity instanceOf Group)
        {
            $entity->setIsActive(true);
        }

        if($entity instanceOf Event)
        {
            $entity->setUser($this->security->getUser());
        }
    }
}