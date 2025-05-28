<?php

declare(strict_types=1);

namespace AppBundle\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * @template TEntity of object
 */
abstract class EntityRepository extends Repository
{
    private readonly EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        Connection $connection,
    ) {
        parent::__construct($connection);

        $this->entityManager = $entityManager;
    }

    abstract protected function entityClass(): string;

    final protected function alias(): string
    {
        $className = basename(str_replace('\\', '/', $this->entityClass()));

        preg_match_all('/[A-Z][a-z0-9]*/', $className, $matches);

        return strtolower(implode('', array_map(fn(string $word) => $word[0], $matches[0])));
    }

    /**
     * @param TEntity $entity
     */
    final public function save(object $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * @return ?TEntity
     */
    final public function find(int|string $id): ?object
    {
        return $this->entityManager->find($this->entityClass(), $id);
    }

    /**
     * @return array<TEntity>
     */
    final public function findAll(): array
    {
        return $this->entityManager->getRepository($this->entityClass())->findAll();
    }

    final protected function createResultMapping(): ResultSetMappingBuilder
    {
        $mapping = new ResultSetMappingBuilder($this->entityManager);
        $mapping->addRootEntityFromClassMetadata($this->entityClass(), $this->alias());

        return $mapping;
    }

    final protected function createEntityQuery(QueryBuilder $queryBuilder, ?ResultSetMapping $resultSetMapping = null): AbstractQuery
    {
        $resultSetMapping ??= $this->createResultMapping();

        $query = $this->entityManager->createNativeQuery($queryBuilder->getSQL(), $resultSetMapping);
        $query->setParameters($queryBuilder->getParameters());

        return $query;
    }
}
