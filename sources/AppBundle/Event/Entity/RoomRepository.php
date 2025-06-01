<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Event\Model\Event;

/**
 * @extends EntityRepository<Room>
 */
final class RoomRepository extends EntityRepository
{
    protected function entityClass(): string
    {
        return Room::class;
    }

    public function findByEvent(Event $event): array
    {
        $query = $this->createEntityQuery(
            ($qb = $this->createQueryBuilder())
                ->from('afup_forum_salle', 'r')
                ->select('r.id', 'r.nom', 'r.id_forum')
                ->where($qb->expr()->eq('r.id_forum', ':idEvent'))
                ->setParameter('idEvent', $event->getId())
        );

        $query->execute();

        return $query->getResult();
    }
}
