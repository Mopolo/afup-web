<?php

declare(strict_types=1);

namespace AppBundle\AssembleeGenerale;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'afup_assemblee_generale_question')]
final class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public int $id;

    #[ORM\Column(type: 'timestamp', nullable: false)]
    public \DateTimeImmutable $date;

    #[ORM\Column(type: 'string', nullable: false)]
    public string $label;

    #[ORM\Column(type: 'datetime', nullable: true)]
    public ?\DateTime $openedAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    public ?\DateTime $closedAt = null;

    #[ORM\Column(type: 'datetime', nullable: false)]
    public \DateTime $createdAt;
}
