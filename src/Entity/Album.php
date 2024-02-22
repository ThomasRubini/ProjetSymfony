<?php

namespace App\Entity;

use App\Repository\AlbumRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlbumRepository::class)]
class Album
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fruits = null;

    #[ORM\Column]
    private ?int $album_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getFruits(): ?string
    {
        return $this->fruits;
    }

    public function setFruits(?string $fruits): static
    {
        $this->fruits = $fruits;

        return $this;
    }

    public function getAlbumId(): ?int
    {
        return $this->album_id;
    }

    public function setAlbumId(int $album_id): static
    {
        $this->album_id = $album_id;

        return $this;
    }
}
