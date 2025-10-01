<?php

/**
 * @copyright 2024 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Date\Doctrine;

use Arokettu\Date\Date;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use DomainException;
use Override;
use RangeException;
use TypeError;
use UnexpectedValueException;

final class DateType extends Type
{
    public const NAME = 'arokettu_date';

    #[Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getDateTypeDeclarationSQL($column);
    }

    #[Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): Date|null
    {
        if ($value === null || $value instanceof Date) {
            return $value;
        }

        try {
            return DateHelper::parse($value);
        } catch (TypeError | DomainException | UnexpectedValueException | RangeException) {
            throw ConversionException::conversionFailedUnserialization(
                static::NAME,
                'Not a valid date representation',
            );
        }
    }

    #[Override]
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
                throw ConversionException::conversionFailedSerialization(
                    $value,
                    self::NAME,
                    'Not a valid date representation',
                    $e,
                );
            }
        }

        throw ConversionException::conversionFailedInvalidType($value, self::NAME, ['null', 'string', Date::class]);
    }

    #[Override]
    public function getName(): string
    {
        return self::NAME;
    }

    #[Override]
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
