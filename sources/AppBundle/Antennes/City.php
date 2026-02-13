<?php

declare(strict_types=1);

namespace AppBundle\Antennes;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class City
{
    #[ORM\Embedded(class: Point::class)]
    public ?Point $firstPoint = null;

    #[ORM\Embedded(class: Point::class)]
    public ?Point $secondPoint = null;

    #[ORM\Embedded(class: Point::class)]
    public ?Point $thirdPoint = null;

    #[ORM\Embedded(class: Position::class)]
    public ?Position $position = null;
}
