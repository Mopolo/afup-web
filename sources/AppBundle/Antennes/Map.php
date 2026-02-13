<?php

declare(strict_types=1);

namespace AppBundle\Antennes;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class Map
{
    #[ORM\Column(nullable: true)]
    public ?bool $useSecondColor = null;

    #[ORM\Column(length: 10, nullable: true, enumType: LegendAttachment::class)]
    public ?LegendAttachment $legendAttachment = null;

    #[ORM\Embedded(class: City::class)]
    public ?City $firstCity = null;

    #[ORM\Embedded(class: City::class)]
    public ?City $secondCity = null;
}
