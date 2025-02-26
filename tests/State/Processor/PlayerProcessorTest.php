<?php

namespace App\Tests\State\Processor;

use ApiPlatform\Metadata\Post;
use App\Entity\Player;
use App\Entity\Team;
use App\State\Processor\PlayerProcessor;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PlayerProcessorTest extends TestCase
{
    public function testProcessReturnsDataForNonPlayer()
    {
        // Create a dummy non-Player object
        $dummyData = new \stdClass();

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $processor = new PlayerProcessor($entityManager);

        // Use a concrete Operation, e.g. Post
        $operation = new Post();
        $result = $processor->process($dummyData, $operation, [], []);

        $this->assertSame($dummyData, $result);
    }

    public function testProcessThrowsExceptionWhenTeamPlayerLimitExceeded()
    {
        // Create a new team and a new player associated with that team
        $team = new Team();
        $player = new Player();
        $player->setTeam($team);

        // Simulate repository count returning 11
        $repository = $this->createMock(EntityRepository::class);
        $repository->expects($this->once())
            ->method('count')
            ->with(['team' => $team])
            ->willReturn(11);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->with(Player::class)
            ->willReturn($repository);

        $processor = new PlayerProcessor($entityManager);

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Cannot add more than 11 players to a team.');

        $operation = new Post();
        $processor->process($player, $operation, [], []);
    }

    public function testProcessPersistsValidPlayer()
    {
        // Create a new team and a new player associated with that team
        $team = new Team();
        $player = new Player();
        $player->setTeam($team);

        // Simulate repository count returning 5 (valid scenario)
        $repository = $this->createMock(EntityRepository::class);
        $repository->expects($this->once())
            ->method('count')
            ->with(['team' => $team])
            ->willReturn(5);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->with(Player::class)
            ->willReturn($repository);

        // Expect persist() and flush() to be called once each
        $entityManager->expects($this->once())
            ->method('persist')
            ->with($player);
        $entityManager->expects($this->once())
            ->method('flush');

        $processor = new PlayerProcessor($entityManager);

        $operation = new Post();
        $result = $processor->process($player, $operation, [], []);

        $this->assertSame($player, $result);
    }
}
