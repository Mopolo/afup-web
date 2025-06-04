<?php

declare(strict_types=1);

namespace AppBundle\AssembleeGenerale;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Doctrine\Repository;

/**
 * @extends Repository<Question>
 */
final class QuestionRepository extends EntityRepository
{
    protected function entityClass(): string
    {
        return Question::class;
    }

    /**
     * @return array<Question>
     */
    public function findAllInPast(): array
    {
        $query = $this->createEntityQuery(
            ($qb = $this->createQueryBuilder())
                ->select('q.*')
                ->from('afup_assemblee_generale_question', $this->alias())
                ->where($qb->expr()->gt('q.opened_at', ':open'))
                ->setParameter('open', new \DateTime('-2 years'))
        );

        $query->execute();

        return $query->getResult();
    }

    public function getAttendeeRaw(string $login, \DateTimeInterface $date): Attendee
    {
        $statement = $this->prepare(<<<'SQL'
SELECT
app.id,
app.email,
app.login,
app.nom,
app.prenom,
app.nearest_office,
apag.date_consultation,
apag.presence,
app2.id as power_id,
app2.nom as power_lastname,
app2.prenom as power_firstname
FROM afup_personnes_physiques app
JOIN afup_presences_assemblee_generale apag ON app.id = apag.id_personne_physique
LEFT JOIN afup_personnes_physiques app2 ON app2.id = apag.id_personne_avec_pouvoir
WHERE app.login = :login AND apag.date = :date
LIMIT 1
SQL
        );

        $statement->bindValue('login', $login);
        $statement->bindValue('date', $date->getTimestamp());

        return $this->mapOne(Attendee::class, $statement);
    }

    public function getAttendee(string $login, \DateTimeInterface $date): Attendee
    {
        $query = ($qb = $this->createQueryBuilder())
            ->select(
                'app.id',
                'app.email',
                'app.login',
                'app.nom',
                'app.prenom',
                'app.nearest_office',
                'apag.date_consultation',
                'apag.presence',
                'app2.id as power_id',
                'app2.nom as power_lastname',
                'app2.prenom as power_firstname',
            )
            ->from('afup_personnes_physiques', 'app')
            ->join('app', 'afup_presences_assemblee_generale', 'apag', 'app.id = apag.id_personne_physique')
            ->leftJoin('apag', 'afup_personnes_physiques', 'app2', 'app2.id = apag.id_personne_avec_pouvoir')
            ->where($qb->expr()->and(
                $qb->expr()->eq('app.login', ':login'),
                $qb->expr()->eq('apag.date', ':date'),
            ))
            ->setParameter('login', $login)
            ->setParameter('date', $date->getTimestamp());

        return $this->mapOne(Attendee::class, $query);
    }
}
