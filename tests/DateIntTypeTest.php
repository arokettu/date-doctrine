<?php

/**
 * @copyright 2024 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Date\Doctrine\Tests;

use Arokettu\Date\Date;
use Arokettu\Date\Doctrine\DateIntType;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\MySQL80Platform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\OraclePlatform;
use Doctrine\DBAL\Platforms\PostgreSQL100Platform;
use Doctrine\DBAL\Platforms\PostgreSQL94Platform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Platforms\SQLServerPlatform;
use Doctrine\DBAL\Types\ConversionException;
use PHPUnit\Framework\TestCase;

final class DateIntTypeTest extends TestCase
{
    public function testName(): void
    {
        $type = new DateIntType();

        self::assertEquals($type::NAME, $type->getName());
    }

    public function testRequireComment(): void
    {
        $type = new DateIntType();
        $platform = new SqlitePlatform();

        self::assertTrue($type->requiresSQLCommentHint($platform));
    }

    public function testBindingType(): void
    {
        $type = new DateIntType();

        self::assertEquals(ParameterType::INTEGER, $type->getBindingType());
    }

    public function testCreation(): void
    {
        $type = new DateIntType();

        $sql = [
            [new SqlitePlatform(), 'INTEGER'],
            [new MySQLPlatform(), 'INT'],
            [new MySQL80Platform(), 'INT'],
            [new PostgreSQLPlatform(), 'INT'],
            [new PostgreSQL94Platform(), 'INT'],
            [new PostgreSQL100Platform(), 'INT'],
            [new MariaDBPlatform(), 'INT'],
            [new SQLServerPlatform(), 'INT'],
            [new OraclePlatform(), 'NUMBER(10)'],
        ];

        $column = ['name' => 'test_test'];

        foreach ($sql as [$platform, $query]) {
            self::assertEquals($query, $type->getSQLDeclaration($column, $platform), $platform::class);
        }
    }

    public function testDbToPHP(): void
    {
        $type = new DateIntType();
        $platform = new SqlitePlatform();

        $date = 2457007; // '2014-12-15';

        self::assertNull($type->convertToPHPValue(null, $platform));

        $dateObj = $type->convertToPHPValue($date, $platform);
        self::assertInstanceOf(Date::class, $dateObj);
        self::assertEquals($date, $dateObj->julianDay);
    }

    public function testDbToPHPWrongType(): void
    {
        $type = new DateIntType();
        $platform = new SqlitePlatform();

        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage(
            "Could not convert database value to 'arokettu_date_int' as an error was triggered " .
            "by the unserialization: 'Not a valid date representation'",
        );
        $type->convertToPHPValue('abcd', $platform);
    }

    public function testPHPToDb(): void
    {
        $type = new DateIntType();
        $platform = new SqlitePlatform();

        $date = 2457007; // '2014-12-15';
        $dateObj = new Date($date);

        self::assertEquals($date, $type->convertToDatabaseValue($date, $platform));
        self::assertEquals($date, $type->convertToDatabaseValue($dateObj, $platform));

        self::assertNull($type->convertToDatabaseValue(null, $platform));
    }

    public function testPHPToDbWrongType(): void
    {
        $type = new DateIntType();
        $platform = new SqlitePlatform();

        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage(
            'Could not convert PHP value 123.45 to type arokettu_date_int. ' .
            'Expected one of the following types: null, int, string, Arokettu\Date\Date',
        );
        $type->convertToDatabaseValue(123.45, $platform);
    }
}
