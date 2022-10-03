<?php

namespace App\Entity;

use App\Repository\PowerRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PowerRepository::class)]
class Power
{
    public function __construct() {}

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('get')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'Power name cannot be empty!')]
    #[Groups('get')]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'power', targetEntity: Hero::class)]
    private ?Collection $heroes = null;

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
