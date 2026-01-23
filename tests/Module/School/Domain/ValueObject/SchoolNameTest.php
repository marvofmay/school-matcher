<?php

declare(strict_types=1);

namespace App\Tests\Module\School\Domain\ValueObject;

use App\Module\School\Domain\ValueObject\SchoolName;
use PHPUnit\Framework\TestCase;

final class SchoolNameTest extends TestCase
{
    public function testExactMatchReturns100(): void
    {
        $name = SchoolName::create('I Liceum Ogólnokształcące');
        $this->assertSame(100, $name->score('I Liceum Ogólnokształcące'));
    }

    public function testPartialMatchReturns80(): void
    {
        $name = SchoolName::create('I Liceum Ogólnokształcące');
        $this->assertSame(80, $name->score('Liceum Ogólnokształcące'));
        $this->assertSame(80, $name->score('I Liceum'));
    }

    public function testSimilarNameReturnsApproximateScore(): void
    {
        $name = SchoolName::create('I Liceum Ogólnokształcące');
        $score = $name->score('I Liceum Oglonokszztalcace');
        $this->assertGreaterThan(70, $score);
        $this->assertLessThan(100, $score);
    }

    public function testCompletelyDifferentNameReturnsLowScore(): void
    {
        $name = SchoolName::create('I Liceum Ogólnokształcące');
        $this->assertLessThan(50, $name->score('Technikum Informatyczne'));
    }

    public function testNormalizeRemovesSpecialCharactersAndExtraSpaces(): void
    {
        $name = SchoolName::create('I Liceum Ogólnokształcące!');
        $this->assertSame(100, $name->score('  I Liceum  Ogolnoksztalcace  '));
    }

    public function testEmptyInputReturns0(): void
    {
        $name = SchoolName::create('I Liceum Ogólnokształcące');
        $this->assertSame(0, $name->score(''));
    }

    public function testCreateStoresValue(): void
    {
        $name = SchoolName::create('I Liceum Ogólnokształcące');
        $this->assertSame('I Liceum Ogólnokształcące', $name->getValue());
    }

    public function testCreateThrowsOnEmptyString(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('School name cannot be empty');
        SchoolName::create('');
    }
}
