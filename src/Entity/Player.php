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
use Symfony\Component\Validator\Constraints as Assert;

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
    #[Assert\NotBlank(message: "First name is required.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "First name cannot be longer than 255 characters."
    )]
    #[ApiProperty(description: "The first name of the player")]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Last name is required.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Last name cannot be longer than 255 characters."
    )]
    #[ApiProperty(description: "The last name of the player")]
    private ?string $lastName = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Age is required.")]
    #[Assert\Positive(message: "Age must be a positive number.")]
    #[ApiProperty(description: "The player's age")]
    private ?int $age = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Position is required.")]
    #[Assert\Choice(
        choices: ["Goalkeeper", "Defender", "Midfielder", "Forward"],
        message: "Invalid position. Choose one of: Goalkeeper, Defender, Midfielder, Forward."
    )]
    #[ApiProperty(description: "The player's position on the field (e.g., Forward, Defender)")]
    private ?string $position = null;

    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'players')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[Assert\NotNull(message: "Player must be assigned to a team.")]
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
