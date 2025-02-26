<?php

namespace App\EventListener;

use App\Entity\Team;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsDoctrineListener(event: Events::postUpdate)]
class TeamUpdateListener
{
    public function __construct(
        #[Autowire(service: "monolog.logger.notifications")]
        private LoggerInterface $notificationsLogger
    ) {
    }

    public function postUpdate(PostUpdateEventArgs $event): void
    {
        $entity = $event->getObject();
        if (!$entity instanceof Team) {
            return;
        }

        $objectManager = $event->getObjectManager();
        $uow = $objectManager->getUnitOfWork();
        $changeset = $uow->getEntityChangeSet($entity);

        if (!isset($changeset['city'])) {
            return;
        }

        $newCity = $changeset['city'][1];
        foreach ($entity->getPlayers() as $player) {
            $message = sprintf(
                'Notification to Player %s %s: Your team %s has been relocated to %s.',
                $player->getFirstName(),
                $player->getLastName(),
                $entity->getName(),
                $newCity
            );
            $this->notificationsLogger->info($message);
        }
    }
}
