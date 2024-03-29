<?php

declare(strict_types=1);

namespace Arokettu\Date\Doctrine;

use Arokettu\Date\Date;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Exception\SerializationFailed;
use Doctrine\DBAL\Types\Exception\ValueNotConvertible;
use Doctrine\DBAL\Types\Type;

final class DateType extends Type
{
    public const NAME = 'arokettu_date';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getDateTypeDeclarationSQL($column);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Date
    {
        if ($value === null || $value instanceof Date) {
            return $value;
        }

        try {
            return DateHelper::parse($value);
        } catch (\TypeError | \DomainException) {
            throw ValueNotConvertible::new(
                $value,
                static::NAME,
                'Not a valid date representation'
            );
        }
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Date) {
            return $value->toString();
        }

        if (\is_string($value) || $value instanceof \Stringable) {
            try {
                $value = DateHelper::parse((string)$value);
                return $value->toString();
            } catch (\TypeError | \UnexpectedValueException | \DomainException $e) {
                throw SerializationFailed::new(
                    $value,
                    static::NAME,
                    'Not a valid date representation',
                    $e
                );
            }
        }

        throw InvalidType::new($value, static::NAME, ['null', 'string', Date::class]);
    }
}
