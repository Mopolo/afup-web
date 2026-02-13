<?php

declare(strict_types=1);

namespace AppBundle\Antennes;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class Socials
{
    #[ORM\Column(length: 255, nullable: true)]
    public ?string $youtube = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $blog = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $twitter = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $linkedin = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $bluesky = null;
}
