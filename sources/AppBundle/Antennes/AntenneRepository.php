<?php

declare(strict_types=1);

namespace AppBundle\Antennes;

use AppBundle\Doctrine\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Antenne>
 */
class AntenneRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Antenne::class);
    }

    /**
     * @return array<string, Antenne>
     */
    public function getAll(): array
    {
        return $this->createQueryBuilder('a', 'a.code')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<string, Antenne>
     */
    public function getAllSortedByLabels(): array
    {
        return $this->createQueryBuilder('a', 'a.code')
            ->where('a.hideOnOfficesPage = false')
            ->orderBy('a.label', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByCode(string $code): Antenne
    {
        $antenne = $this->findOneBy(['code' => $code]);

        if ($antenne === null) {
            throw new \InvalidArgumentException("Antenne introuvable via le code $code");
        }

        return $antenne;
    }

    /**
     * @return array<string, string>
     */
    public function getOrderedLabelsByKey(): array
    {
        $labels = [];
        foreach ($this->getAllSortedByLabels() as $antenne) {
            $labels[$antenne->code] = $antenne->label;
        }

        return $labels;
    }
}
