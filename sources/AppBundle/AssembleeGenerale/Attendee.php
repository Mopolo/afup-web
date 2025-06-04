<?php

declare(strict_types=1);

namespace AppBundle\AssembleeGenerale;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\MappedSuperclass;

final readonly class Attendee
{
    public function __construct(
        public int $id,
        public string $email,
        public string $login,
        public string $nom,
        public string $prenom,
        public string $nearestOffice,
        public int $dateConsultation,
        public bool $presence,
        public ?int $powerId,
        public ?string $powerLastname,
        public ?string $powerFirstname,
    ) {}
}
