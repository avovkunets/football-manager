<?php

namespace App\Tests\Functional;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Player;
use App\Entity\Team;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class PlayerApiTest extends ApiTestCase
{
    /**
     * @throws TransportExceptionInterface
     */
    public function testGetPlayers(): void
    {
        // Create a team and a player.
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $team = new Team();
        $team->setName('FC Test');
        $team->setCity('City');
        $team->setYearFounded(2000);
        $team->setStadiumName('Stadium');
        $entityManager->persist($team);

        $player = new Player();
        $player->setFirstName('John');
        $player->setLastName('Doe');
        $player->setAge(25);
        $player->setPosition('Forward');
        $player->setTeam($team);
        $entityManager->persist($player);
        $entityManager->flush();

        $client = static::createClient();
        $client->request('GET', '/api/players');
        $this->assertResponseIsSuccessful();
    }

    public function testCreatePlayerExceedsLimit(): void
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $team = new Team();
        $team->setName('FC Limit');
        $team->setCity('City');
        $team->setYearFounded(2000);
        $team->setStadiumName('Stadium');
        $entityManager->persist($team);
        $entityManager->flush();

        // Create 11 players for this team.
        for ($i = 0; $i < 11; $i++) {
            $player = new Player();
            $player->setFirstName('Player '.$i);
            $player->setLastName('Test');
            $player->setAge(20 + $i);
            $player->setPosition('Midfielder');
            $player->setTeam($team);
            $entityManager->persist($player);
        }
        $entityManager->flush();

        $client = static::createClient();
        $client->request('POST', '/api/players', ['json' => [
            'firstName' => 'Extra',
            'lastName' => 'Player',
            'age' => 30,
            'position' => 'Forward',
            'team' => '/api/teams/'.$team->getId()
        ]]);
        // Our custom processor should reject this, returning a 400 error.
        $this->assertResponseStatusCodeSame(400);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testCreateValidPlayer(): void
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $team = new Team();
        $team->setName('FC Valid');
        $team->setCity('City');
        $team->setYearFounded(2000);
        $team->setStadiumName('Stadium');
        $entityManager->persist($team);
        $entityManager->flush();

        $client = static::createClient();
        $client->request('POST', '/api/players', ['json' => [
            'firstName' => 'Valid',
            'lastName' => 'Player',
            'age' => 25,
            'position' => 'Defender',
            'team' => '/api/teams/'.$team->getId()
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['firstName' => 'Valid']);
    }
}
