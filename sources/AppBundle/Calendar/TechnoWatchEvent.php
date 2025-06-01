<?php

declare(strict_types=1);

namespace AppBundle\Calendar;

final readonly class TechnoWatchEvent
{
    public function __construct(
        public \DateTimeImmutable $date,
        public string $firstChair,
        public string $secondChair,
    ) {
    }
}
