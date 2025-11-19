<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Entity\Repository;

use AppBundle\Accounting\Entity\Rule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Rule>
 */
final class RuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rule::class);
    }

    /**
     * @return array<Rule>
     */
    public function getAllSortedByName(): array
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.label', 'asc')
            ->getQuery()
            ->execute();
    }

    public function save(Rule $rule): void
    {
//        dd($rule);
        $this->getEntityManager()->persist($rule);

        try {
            $this->getEntityManager()->flush();
        } catch (\Throwable $e) {
            dd($e);
        }
    }
}
