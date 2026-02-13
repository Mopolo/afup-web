<?php

declare(strict_types=1);

namespace AppBundle\Antennes;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AntenneRepository::class)]
#[ORM\Table(name: 'antennes')]
class Antenne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    public string $code;

    #[ORM\Column(length: 100)]
    public string $label;

    #[ORM\Column(length: 255)]
    public string $logoUrl;

    #[ORM\Column]
    public bool $hideOnOfficesPage = false;

    /** @var string[]|null */
    #[ORM\Column(type: 'json', nullable: true)]
    public ?array $departments = null;

    /** @var string[]|null */
    #[ORM\Column(type: 'json', nullable: true)]
    public ?array $pays = null;

    #[ORM\Embedded(class: Meetup::class)]
    public ?Meetup $meetup = null;

    #[ORM\Embedded(class: Socials::class)]
    public ?Socials $socials = null;

    #[ORM\Embedded(class: Map::class)]
    public ?Map $map = null;
}
