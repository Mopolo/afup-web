<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'afup_forum_salle')]
final class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public int $id;

    #[ORM\Column(name: 'nom', type: 'string', nullable: false)]
    public string $name;

    #[ORM\Column(name: 'id_forum', type: 'integer', nullable: false)]
    public int $eventId;

    public function __construct(int $eventId)
    {
        $this->eventId = $eventId;
    }
}
