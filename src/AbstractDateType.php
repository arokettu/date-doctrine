<?php

/**
 * @copyright 2024 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Date\Doctrine;

use Arokettu\Date\Date;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Exception\SerializationFailed;
use Doctrine\DBAL\Types\Type;
use DomainException;
use Override;
use RangeException;
use TypeError;
use UnexpectedValueException;

abstract class AbstractDateType extends Type
{
    public const NAME = ''; // override

    abstract protected function dateToDB(Date $date): mixed;

    #[Override]
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if ($value === null) {
            return null;
        }

        switch (true) {
            case $value instanceof Date:
                // fall through
                break;

            case \is_int($value):
                $value = new Date($value);
                break;

            case \is_string($value):
            case $value instanceof \Stringable:
                try {
                    $value = DateHelper::parse((string)$value);
                } catch (TypeError | DomainException | UnexpectedValueException | RangeException $e) {
                    throw SerializationFailed::new(
                        $value,
                        static::NAME,
                        'Not a valid date representation',
                        $e,
                    );
                }
                break;

            default:
                throw InvalidType::new($value, static::NAME, ['null', 'int', 'string', Date::class]);
        }

        return $this->dateToDB($value);
    }
}
