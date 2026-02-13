<?php

declare(strict_types=1);

namespace AppBundle\Antennes;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class Meetup
{
    #[ORM\Column(length: 255, nullable: true)]
    public ?string $urlName = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $id = null;
}
