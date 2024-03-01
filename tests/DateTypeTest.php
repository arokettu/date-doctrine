<?php

declare(strict_types=1);

namespace Arokettu\Date\Doctrine\Tests;

use Arokettu\Date\Date;
use Arokettu\Date\Doctrine\DateType;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\MySQL80Platform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\OraclePlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\DBAL\Platforms\SQLServerPlatform;
use Doctrine\DBAL\Types\ConversionException;
use PHPUnit\Framework\TestCase;

class DateTypeTest extends TestCase
{
    public function testBindingType(): void
    {
        $type = new DateType();

        self::assertEquals(ParameterType::STRING, $type->getBindingType());
    }

    public function testCreation(): void
    {
        $type = new DateType();

        $sql = [
            [new SQLitePlatform(), 'DATE'],
            [new MySQLPlatform(), 'DATE'],
            [new MySQL80Platform(), 'DATE'],
            [new PostgreSQLPlatform(), 'DATE'],
            [new MariaDBPlatform(), 'DATE'],
            [new SQLServerPlatform(), 'DATE'],
            [new OraclePlatform(), 'DATE'],
        ];

        $column = ['name' => 'test_test'];

        foreach ($sql as [$platform, $query]) {
            self::assertEquals($query, $type->getSQLDeclaration($column, $platform), $platform::class);
        }
    }

    public function testDbToPHP(): void
    {
        $type = new DateType();
        $platform = new SQLitePlatform();

        $date = '2014-12-15';

        self::assertNull($type->convertToPHPValue(null, $platform));

        $dateObj = $type->convertToPHPValue($date, $platform);
        self::assertInstanceOf(Date::class, $dateObj);
        self::assertEquals($date, $dateObj->toString());
    }

    public function testDbToPHPWrongType(): void
    {
        $type = new DateType();
        $platform = new SQLitePlatform();

        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage(
            'Could not convert database value to "arokettu_date" as an error was triggered by the unserialization: ' .
            'Not a valid date representation'
        );
        $type->convertToPHPValue(123, $platform);
    }

    public function testDbToPHPWrongFormat(): void
    {
        $type = new DateType();
        $platform = new SQLitePlatform();

        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage(
            'Could not convert database value to "arokettu_date" as an error was triggered by the unserialization: ' .
            'Not a valid date representation'
        );
        $type->convertToPHPValue('2015-15-15', $platform);
    }

    public function testPHPToDb(): void
    {
        $type = new DateType();
        $platform = new SQLitePlatform();

        $date = '2014-12-15';
        $dateObj = Date::parse($date);
        $stringable = new class () {
            public function __toString(): string
            {
                return '2014-12-15';
            }
        };

        self::assertEquals($date, $type->convertToDatabaseValue($date, $platform));
        self::assertEquals($date, $type->convertToDatabaseValue($dateObj, $platform));
        self::assertEquals($date, $type->convertToDatabaseValue($stringable, $platform));

        self::assertNull($type->convertToDatabaseValue(null, $platform));
    }

    public function testPHPToDbWrongType(): void
    {
        $type = new DateType();
        $platform = new SQLitePlatform();

        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage(
            'Could not convert PHP value 123 to type arokettu_date. ' .
            'Expected one of the following types: null, string, Arokettu\Date\Date'
        );
        $type->convertToDatabaseValue(123, $platform);
    }

    public function testPHPToDbWrongFormat(): void
    {
        $type = new DateType();
        $platform = new SQLitePlatform();

        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage(
            'Could not convert PHP type "string" to "arokettu_date". ' .
            'An error was triggered by the serialization: Not a valid date representation'
        );
        $type->convertToDatabaseValue('2015-15-15', $platform);
    }
}
