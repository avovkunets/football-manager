<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Player;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

readonly class PlayerProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!$data instanceof Player) {
            return $data;
        }

        if (null === $data->getId()) {
            $team = $data->getTeam();
            if ($team) {
                $count = $this->em->getRepository(Player::class)->count(['team' => $team]);
                if ($count >= 11) {
                    throw new BadRequestHttpException('Cannot add more than 11 players to a team.');
                }
            }
        }

        $this->em->persist($data);
        $this->em->flush();

        return $data;
    }
}
