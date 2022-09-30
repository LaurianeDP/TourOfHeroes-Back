<?php

namespace App\Entity;

use App\Repository\PowerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PowerRepository::class)]
class Power
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\OneToMany(mappedBy: 'power', targetEntity: Hero::class)]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'Power name cannot be empty!')]
    private ?string $name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
