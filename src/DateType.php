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
use DomainException;
use Override;
use RangeException;
use TypeError;
use UnexpectedValueException;

final class DateType extends AbstractDateType
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
                self::NAME,
                'Not a valid date representation',
            );
        }
    }

    #[Override]
    protected function dateToDB(Date $date): string
    {
        return $date->toString();
    }
}
