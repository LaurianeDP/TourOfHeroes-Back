<?php

namespace App\Entity;

use App\Repository\HeroRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: HeroRepository::class)]
class Hero
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message:'Hero name cannot be empty!')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Hero name must be over {{ limit }} characters long',
        maxMessage: 'Hero name cannot be longer than {{ limit }} characters',
    )]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: Power::class, inversedBy: 'id')]
    #[ORM\JoinColumn]
    #[Assert\NotBlank(message:'Hero power cannot be empty!')]
//    #[Assert\Type(type: Power::class, message:'Hero power must be part of the pre-approved list of powers')]
    private ?string $power = null;
//    private ?Power $power = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: 'Hero Alter Ego must be over {{ limit }} characters long',
        maxMessage: 'Hero Alter Ego cannot be longer than {{ limit }} characters',
    )]
    private ?string $alterEgo = null;

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

    public function getPower(): ?string
    {
        return $this->power;
    }

    public function setPower(string $power): self
    {
        $this->power = $power;

        return $this;
    }

    public function getAlterEgo(): ?string
    {
        return $this->alterEgo;
    }

    public function setAlterEgo(?string $alterEgo): self
    {
        $this->alterEgo = $alterEgo;

        return $this;
    }
}
