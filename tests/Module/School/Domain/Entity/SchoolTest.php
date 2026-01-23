<?php

declare(strict_types=1);

namespace App\Tests\Module\School\Domain\Entity;

use App\Module\School\Domain\Entity\School;
use App\Module\School\Domain\ValueObject\SchoolName;
use PHPUnit\Framework\TestCase;

final class SchoolTest extends TestCase
{
    public function testCreateSchoolSuccess(): void
    {
        $official = SchoolName::create('I Liceum Ogólnokształcące');
        $alt = [SchoolName::create('LO Mickiewicza')];

        $school = School::create($official, $alt, 'Warszawa', 'liceum');

        $this->assertSame($official, $school->getOfficialName());
        $this->assertSame('Warszawa', $school->getCity());
        $this->assertSame('liceum', $school->getType());
        $this->assertCount(2, $school->getAllNames());
    }

    public function testCreateSchoolEmptyCityThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('City cannot be empty');

        School::create(SchoolName::create('Test'), [], '', 'liceum');
    }

    public function testCreateSchoolEmptyTypeThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Type cannot be empty');

        School::create(SchoolName::create('Test'), [], 'Warszawa', '');
    }
}
