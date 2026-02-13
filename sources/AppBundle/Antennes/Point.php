<?php

declare(strict_types=1);

namespace AppBundle\Antennes;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class Point
{
    #[ORM\Column(nullable: true)]
    public ?int $x = null;

    #[ORM\Column(nullable: true)]
    public ?int $y = null;
}
