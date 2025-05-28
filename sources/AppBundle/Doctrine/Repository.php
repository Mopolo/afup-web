<?php

declare(strict_types=1);

namespace AppBundle\Doctrine;

use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\Mapper\TreeMapper;
use CuyZ\Valinor\MapperBuilder;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Statement;


abstract class Repository
{
    private readonly Connection $connection;
    private readonly TreeMapper $mapper;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->mapper = (new MapperBuilder())
            ->enableFlexibleCasting() // todo v2 https://github.com/CuyZ/Valinor/pull/642
            ->mapper();
    }

    final protected function prepare(string $sql): Statement
    {
        return $this->connection->prepare($sql);
    }

    final protected function createQueryBuilder(): QueryBuilder
    {
        return $this->connection->createQueryBuilder();
    }

    /**
     * @template T of object
     * @param class-string<T> $type
     * @return ?T
     */
    final protected function mapOne(string $type, QueryBuilder|Statement $queryBuilderOrStatement): ?object
    {
        $result = $queryBuilderOrStatement->executeQuery()->fetchAssociative();

        if ($result === false) {
            return null;
        }

        return $this->mapper->map($type, Source::array($result)->camelCaseKeys());
    }

    /**
     * @template T of object
     * @param class-string<T> $type
     * @return list<T>
     */
    final protected function mapMany(string $type, QueryBuilder|Statement $queryBuilderOrStatement): array
    {
        $result = $queryBuilderOrStatement->executeQuery()->fetchAllAssociative();

        if ($result === []) {
            return [];
        }

        return $this->mapper->map('list<' . $type . '>', Source::array($result)->camelCaseKeys());
    }
}
