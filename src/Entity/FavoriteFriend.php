<?php

namespace App\Entity;

use App\Repository\FavoriteFriendRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FavoriteFriendRepository::class)]
class FavoriteFriend
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?User $owner = null;

    #[ORM\ManyToOne]
    private ?Friendship $friendship = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getFriendship(): ?Friendship
    {
        return $this->friendship;
    }

    public function setFriendship(?Friendship $friendship): static
    {
        $this->friendship = $friendship;

        return $this;
    }
}
