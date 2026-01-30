<?php

declare(strict_types=1);

namespace App\Tests\Module\School\Domain\Service;

use App\Module\School\Domain\Entity\School;
use App\Module\School\Domain\Service\SchoolMatcher;
use App\Module\School\Domain\ValueObject\SchoolMatch;
use App\Module\School\Domain\ValueObject\SchoolName;
use PHPUnit\Framework\TestCase;

final class SchoolMatcherTest extends TestCase
{
    private SchoolMatcher $matcher;

    protected function setUp(): void
    {
        $schools = [
            School::create(
                SchoolName::create('I Liceum Ogólnokształcące im. Adama Mickiewicza'),
                [
                    SchoolName::create('LO Mickiewicza'),
                    SchoolName::create('Mickiewicz'),
                    SchoolName::create('I LO')
                ],
                'Warszawa',
                'liceum'
            ),
            School::create(
                SchoolName::create('XIV Liceum Ogólnokształcące im. Stanisława Staszica'),
                [
                    SchoolName::create('Staszic'),
                    SchoolName::create('XIV LO'),
                    SchoolName::create('14 LO')
                ],
                'Warszawa',
                'liceum'
            ),
        ];

        $this->matcher = new SchoolMatcher($schools);
    }

    public function testExactNameMatch(): void
    {
        $results = $this->matcher->match('I Liceum Ogólnokształcące im. Adama Mickiewicza');

        $this->assertNotEmpty($results);
        $this->assertInstanceOf(SchoolMatch::class, $results[0]);

        $this->assertSame(
            'I Liceum Ogólnokształcące im. Adama Mickiewicza',
            $results[0]->school()->getOfficialName()->getValue()
        );

        $this->assertSame(100, $results[0]->score()->value());
    }

    public function testPartialNameMatch(): void
    {
        $results = $this->matcher->match('Mickiewicz');

        $this->assertNotEmpty($results);
        $this->assertSame(
            'I Liceum Ogólnokształcące im. Adama Mickiewicza',
            $results[0]->school()->getOfficialName()->getValue()
        );
    }

    public function testCityFilterExcludesOtherCities(): void
    {
        $this->assertSame([], $this->matcher->match('Staszic', 'Kraków'));
        $this->assertNotEmpty($this->matcher->match('Staszic', 'Warszawa'));
    }

    public function testTypeFilterExcludesOtherTypes(): void
    {
        $this->assertSame([], $this->matcher->match('Staszic', null, 'technikum'));
        $this->assertNotEmpty($this->matcher->match('Staszic', null, 'liceum'));
    }

    public function testNoMatchReturnsEmptyArray(): void
    {
        $this->assertSame([], $this->matcher->match('Nieistniejąca Szkoła'));
    }

    public function testSortingByScore(): void
    {
        $schools = [
            School::create(SchoolName::create('Alpha School'), [SchoolName::create('Alpha')], 'Warszawa', 'liceum'),
            School::create(SchoolName::create('Beta School'), [SchoolName::create('Beta')], 'Warszawa', 'liceum'),
        ];

        $matcher = new SchoolMatcher($schools);
        $results = $matcher->match('Alp');

        $this->assertNotEmpty($results);

        $this->assertSame(
            'Alpha School',
            $results[0]->school()->getOfficialName()->getValue()
        );

        $this->assertGreaterThanOrEqual(
            $results[1]->score()->value(),
            $results[0]->score()->value()
        );
    }

    public function testAliasMatching(): void
    {
        $results = $this->matcher->match('LO Mickiewicza');

        $this->assertNotEmpty($results);
        $this->assertSame(
            'I Liceum Ogólnokształcące im. Adama Mickiewicza',
            $results[0]->school()->getOfficialName()->getValue()
        );
    }

    public function testCaseInsensitiveMatching(): void
    {
        $results = $this->matcher->match('i liceum ogólnokształcące IM. adama mickiewicza');

        $this->assertNotEmpty($results);
        $this->assertSame(
            'I Liceum Ogólnokształcące im. Adama Mickiewicza',
            $results[0]->school()->getOfficialName()->getValue()
        );
    }

    public function testNormalizationIgnoresSpecialCharacters(): void
    {
        $results = $this->matcher->match('Mickiewicz!');

        $this->assertNotEmpty($results);
        $this->assertSame(
            'I Liceum Ogólnokształcące im. Adama Mickiewicza',
            $results[0]->school()->getOfficialName()->getValue()
        );
    }

    public function testMultipleMatchesReturnAllAboveThreshold(): void
    {
        $results = $this->matcher->match('LO');

        $this->assertCount(2, $results);
    }
}
