<?php

declare(strict_types=1);

namespace Arokettu\Date\Doctrine;

use Arokettu\Date\Date;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Exception\SerializationFailed;
use Doctrine\DBAL\Types\Exception\ValueNotConvertible;
use Doctrine\DBAL\Types\Type;
use DomainException;
use RangeException;
use TypeError;
use UnexpectedValueException;

final class DateType extends Type
{
    public const NAME = 'arokettu_date';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getDateTypeDeclarationSQL($column);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): Date|null
    {
        if ($value === null || $value instanceof Date) {
            return $value;
        }

        try {
            return DateHelper::parse($value);
        } catch (TypeError | DomainException | UnexpectedValueException | RangeException) {
            throw ValueNotConvertible::new(
                $value,
                self::NAME,
                'Not a valid date representation'
            );
        }
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): string|null
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
            } catch (TypeError | DomainException | UnexpectedValueException | RangeException $e) {
                throw SerializationFailed::new(
                    $value,
                    self::NAME,
                    'Not a valid date representation',
                    $e
                );
            }
        }

        throw InvalidType::new($value, self::NAME, ['null', 'string', Date::class]);
    }
}
