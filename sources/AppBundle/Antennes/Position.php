<?php

declare(strict_types=1);

namespace AppBundle\Antennes;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class Position
{
    #[ORM\Column(nullable: true)]
    public ?float $latitude = null;

    #[ORM\Column(nullable: true)]
    public ?float $longitude = null;
}
