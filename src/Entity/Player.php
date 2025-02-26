<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\PlayerRepository;
use App\State\Processor\PlayerProcessor;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),                              // GET /players
        new Post(processor: PlayerProcessor::class),      // POST /players
        new Get(),                                        // GET /players/{id}
        new Patch(processor: PlayerProcessor::class),     // PATCH /players/{id}
        new Delete()                                      // DELETE /players/{id}
    ]
)]
class Player
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[ApiProperty(description: "Unique identifier of the player")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[ApiProperty(description: "The first name of the player")]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[ApiProperty(description: "The last name of the player")]
    private ?string $lastName = null;

    #[ORM\Column]
    #[ApiProperty(description: "The player's age")]
    private ?int $age = null;

    #[ORM\Column(length: 255)]
    #[ApiProperty(description: "The player's position on the field (e.g., Forward, Defender)")]
    private ?string $position = null;

    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'players')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Team $team = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): static
    {
        $this->team = $team;

        return $this;
    }
}
