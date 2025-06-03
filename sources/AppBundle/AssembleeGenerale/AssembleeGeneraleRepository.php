<?php

declare(strict_types=1);

namespace AppBundle\AssembleeGenerale;

use AppBundle\Doctrine\Repository;
use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Component\Clock\DatePoint;

final class AssembleeGeneraleRepository extends Repository
{
    /**
     * @return DateTimeInterface[]
     */
    public function getAllDates(): array
    {
        $query = $this->createQueryBuilder()
            ->select('apag.date')
            ->distinct()
            ->from('afup_presences_assemblee_generale', 'apag')
            ->orderBy('apag.date', 'desc');

        return array_map(static fn(array $row): DateTimeImmutable => DatePoint::createFromTimestamp($row['date']), $query->fetchAllAssociative());
    }

    public function countAttendees(DateTimeInterface $date): int
    {
        $query = $this->prepare(<<<'SQL'
SELECT COUNT(*) c
FROM afup_presences_assemblee_generale apag
WHERE apag.date = :date
AND apag.presence = '1' 
SQL
        );

        $query->bindValue('date', $date->getTimestamp());

        return (int) $query->executeQuery()->fetchOne();
    }
}
