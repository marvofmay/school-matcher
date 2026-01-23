<?php

declare(strict_types=1);

namespace App\Tests\Module\School\Application\QueryHandler;

use App\Module\School\Domain\Entity\School;
use App\Module\School\Domain\Service\SchoolMatcher;
use App\Module\School\Domain\ValueObject\SchoolName;
use PHPUnit\Framework\TestCase;

final class SchoolMatcherQueryHandlerTest extends TestCase
{
    public function testMatchReturnsArrayWithScore(): void
    {
        $school = School::create(
            SchoolName::create('I Liceum Ogólnokształcące'),
            [SchoolName::create('LO Mickiewicza')],
            'Warszawa',
            'liceum'
        );

        $matcher = new SchoolMatcher([$school]);

        $matches = $matcher->match('I Liceum Ogólnokształcące');

        $this->assertCount(1, $matches);

        $this->assertArrayHasKey('school', $matches[0]);
        $this->assertArrayHasKey('score', $matches[0]);

        $this->assertSame('I Liceum Ogólnokształcące', $matches[0]['school']);
        $this->assertGreaterThanOrEqual(70, $matches[0]['score']);
    }
}
