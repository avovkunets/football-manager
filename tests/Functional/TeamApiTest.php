<?php

namespace App\Tests\Functional;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Team;
use Doctrine\ORM\EntityManagerInterface;

class TeamApiTest extends ApiTestCase
{
    public function testGetTeams(): void
    {
        // Create a team to ensure there is at least one in the database.
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $team = new Team();
        $team->setName('FC Test');
        $team->setCity('Test City');
        $team->setYearFounded(2000);
        $team->setStadiumName('Test Stadium');
        $entityManager->persist($team);
        $entityManager->flush();

        // Request the GET /api/teams endpoint.
        $client = static::createClient();
        $client->request('GET', '/api/teams');
        $this->assertResponseIsSuccessful();
        // The default content type is JSON-LD.
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testCreateTeam(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/teams', ['json' => [
            'name' => 'FC Example',
            'city' => 'Example City',
            'yearFounded' => 1990,
            'stadiumName' => 'Example Stadium'
        ]]);
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['name' => 'FC Example']);
    }

    public function testUpdateTeamCityTriggersNotification(): void
    {
        // Create a team.
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $team = new Team();
        $team->setName('FC Test');
        $team->setCity('Old City');
        $team->setYearFounded(2000);
        $team->setStadiumName('Test Stadium');
        $entityManager->persist($team);
        $entityManager->flush();

        $client = static::createClient();
        // Update the team's city. The event listener will log notifications.
        $client->request('PATCH', '/api/teams/'.$team->getId(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => ['city' => 'New City']
        ]);

        $this->assertResponseIsSuccessful();
    }

    public function testDeleteTeam(): void
    {
        // Create a team.
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $team = new Team();
        $team->setName('FC Delete');
        $team->setCity('City');
        $team->setYearFounded(2000);
        $team->setStadiumName('Stadium');
        $entityManager->persist($team);
        $entityManager->flush();

        $client = static::createClient();
        $client->request('DELETE', '/api/teams/'.$team->getId());
        $this->assertResponseStatusCodeSame(204);
    }
}
