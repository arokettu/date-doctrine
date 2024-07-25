<?php

declare(strict_types=1);

namespace Arokettu\Date\Doctrine;

use Arokettu\Date\Date;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

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
        } catch (\TypeError | \DomainException) {
            throw ConversionException::conversionFailedUnserialization(
                static::NAME,
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
            } catch (\TypeError | \UnexpectedValueException | \DomainException $e) {
                throw ConversionException::conversionFailedSerialization(
                    $value,
                    self::NAME,
                    'Not a valid date representation',
                    $e
                );
            }
        }

        throw ConversionException::conversionFailedInvalidType($value, self::NAME, ['null', 'string', Date::class]);
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
