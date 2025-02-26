<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource]
#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[ApiProperty(description: "Unique identifier of the team")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[ApiProperty(description: "The name of the team")]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[ApiProperty(description: "The city where the team is based")]
    private ?string $city = null;

    #[ORM\Column]
    #[ApiProperty(description: "The year when the team was founded")]
    private ?int $yearFounded = null;

    #[ORM\Column(length: 255)]
    #[ApiProperty(description: "The name of the team's stadium")]
    private ?string $stadiumName = null;

    /**
     * @var Collection<int, Player>
     */
    #[ORM\OneToMany(targetEntity: Player::class, mappedBy: 'team', orphanRemoval: true)]
    #[ApiProperty(description: "List of players belonging to the team")]
    private Collection $players;

    public function __construct()
    {
        $this->players = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getYearFounded(): ?int
    {
        return $this->yearFounded;
    }

    public function setYearFounded(int $yearFounded): static
    {
        $this->yearFounded = $yearFounded;

        return $this;
    }

    public function getStadiumName(): ?string
    {
        return $this->stadiumName;
    }

    public function setStadiumName(string $stadiumName): static
    {
        $this->stadiumName = $stadiumName;

        return $this;
    }

    /**
     * @return Collection<int, Player>
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(Player $player): static
    {
        if (!$this->players->contains($player)) {
            $this->players->add($player);
            $player->setTeam($this);
        }

        return $this;
    }

    public function removePlayer(Player $player): static
    {
        $this->players->removeElement($player);

        return $this;
    }
}
