<?php

declare(strict_types=1);

namespace AppBundle\Doctrine;

use DateTimeImmutable;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateTimeImmutableType;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Exception;
use Symfony\Component\Clock\DatePoint;

final class TimestampType extends DateTimeImmutableType
{
    private const TYPE_NAME = 'timestamp';

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof DateTimeImmutable) {
            return $value->format('U');
        }

        throw InvalidType::new(
            $value,
            static::class,
            ['null', DateTimeImmutable::class],
        );
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?DateTimeImmutable
    {
        if ($value === null || $value instanceof DateTimeImmutable) {
            return $value;
        }

        $dateTime = DatePoint::createFromTimestamp($value);

        if ($dateTime !== false) {
            return $dateTime;
        }

        try {
            return new DateTimeImmutable($value);
        } catch (Exception $e) {
            throw InvalidFormat::new(
                $value,
                static::class,
                'U',
                $e,
            );
        }
    }

    public function getName(): string
    {
        return self::TYPE_NAME;
    }

//    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
//    {
//        return true;
//    }
}
