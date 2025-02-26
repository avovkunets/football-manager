<?php

namespace App\Tests\EventListener;

use App\Entity\Team;
use App\Entity\Player;
use App\EventListener\TeamUpdateListener;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class TeamUpdateListenerTest extends TestCase
{
    public function testPostUpdateLogsNotificationWhenCityChanges(): void
    {
        // Create a team instance
        $team = new Team();
        $team->setName('FC Test');
        $team->setCity('Old City');
        $team->setYearFounded(2000);
        $team->setStadiumName('Test Stadium');

        // Create a player and add to the team
        $player = new Player();
        $player->setFirstName('John');
        $player->setLastName('Doe');
        $player->setAge(25);
        $player->setPosition('Forward');
        $player->setTeam($team);
        $team->addPlayer($player);

        // Define the changeset: city changed from 'Old City' to 'New City'
        $changeset = ['city' => ['Old City', 'New City']];

        // Create a mock for EntityManagerInterface
        $entityManager = $this->createMock(EntityManagerInterface::class);

        // Create a mock for UnitOfWork and configure it to return the changeset
        $unitOfWork = $this->createMock(UnitOfWork::class);
        $unitOfWork->method('getEntityChangeSet')
            ->with($team)
            ->willReturn($changeset);

        // Set expectations so that EntityManager returns UnitOfWork
        $entityManager->method('getUnitOfWork')->willReturn($unitOfWork);

        // Create a real PostUpdateEventArgs instance
        $postUpdateEventArgs = new PostUpdateEventArgs($team, $entityManager);

        // Create a logger mock and expect an info log with the notification message
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('info')
            ->with($this->stringContains('Notification to Player John Doe: Your team FC Test has been relocated to New City.'));

        // Instantiate the listener with the logger and call postUpdate
        $listener = new TeamUpdateListener($logger);
        $listener->postUpdate($postUpdateEventArgs);
    }
}
