<?php

declare(strict_types=1);

namespace App\Tests\Module\School\Infrastructure\Factory;

use App\Module\School\Domain\Entity\School;
use App\Module\School\Domain\ValueObject\SchoolMatch;
use App\Module\School\Domain\ValueObject\SchoolName;
use App\Module\School\Infrastructure\Factory\SchoolMatcherFactory;
use App\Module\School\Infrastructure\Interface\SchoolLoaderInterface;
use PHPUnit\Framework\TestCase;

final class SchoolMatcherFactoryTest extends TestCase
{
    public function testFactoryReturnsSchoolMatcherWithLoadedSchools(): void
    {
        $loader = new class () implements SchoolLoaderInterface {
            public function loadFromFile(string $filePath): array
            {
                $school1 = School::create(
                    SchoolName::create('Alpha School'),
                    [SchoolName::create('Alpha')],
                    'Warszawa',
                    'liceum'
                );

                $school2 = School::create(
                    SchoolName::create('Beta School'),
                    [SchoolName::create('Beta')],
                    'Warszawa',
                    'liceum'
                );

                return [$school1, $school2];
            }
        };

        $schoolsFile = '/fake/path/schools.txt';

        $factory = new SchoolMatcherFactory($loader, $schoolsFile);
        $matcher = $factory->__invoke();

        $results = $matcher->match('Alpha');
        $this->assertNotEmpty($results);

        $match = $results[0];
        $this->assertInstanceOf(SchoolMatch::class, $match);
        $this->assertSame('Alpha School', $match->school());
        $this->assertGreaterThan(0, $match->score());

        $results = $matcher->match('Beta');
        $this->assertNotEmpty($results);

        $match = $results[0];
        $this->assertInstanceOf(SchoolMatch::class, $match);
        $this->assertSame('Beta School', $match->school());
        $this->assertGreaterThan(0, $match->score());
    }
}
