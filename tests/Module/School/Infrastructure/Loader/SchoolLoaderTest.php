<?php

declare(strict_types=1);

namespace App\Tests\Module\School\Infrastructure\Loader;

use App\Module\School\Domain\Entity\School;
use App\Module\School\Domain\ValueObject\SchoolName;
use App\Module\School\Infrastructure\Loader\SchoolLoader;
use PHPUnit\Framework\TestCase;

final class SchoolLoaderTest extends TestCase
{
    private string $tempFile;

    protected function setUp(): void
    {
        $this->tempFile = tempnam(sys_get_temp_dir(), 'schools_test_');

        $data = <<<TXT
# Test file for SchoolLoader
I Liceum Ogólnokształcące im. Adama Mickiewicza | LO Mickiewicza, Mickiewicz, I LO, Pierwsze LO | Warszawa | liceum
Zespół Szkół Elektronicznych i Informatycznych | ZSEI, Elektronik, ZSEiI | Warszawa | technikum
TXT;

        file_put_contents($this->tempFile, $data);
    }

    protected function tearDown(): void
    {
        unlink($this->tempFile);
    }

    public function testLoadFromFileReturnsSchoolObjects(): void
    {
        $loader = new SchoolLoader();

        $schools = $loader->loadFromFile($this->tempFile);

        $this->assertCount(2, $schools);
        foreach ($schools as $school) {
            $this->assertInstanceOf(School::class, $school);
            $this->assertInstanceOf(SchoolName::class, $school->getOfficialName());
        }


        $firstSchool = $schools[0];
        $this->assertSame('I Liceum Ogólnokształcące im. Adama Mickiewicza', $firstSchool->getOfficialName()->getValue());
        $this->assertSame('Warszawa', $firstSchool->getCity());
        $this->assertSame('liceum', $firstSchool->getType());

        $secondSchool = $schools[1];
        $this->assertSame('Zespół Szkół Elektronicznych i Informatycznych', $secondSchool->getOfficialName()->getValue());
        $this->assertSame('Warszawa', $secondSchool->getCity());
        $this->assertSame('technikum', $secondSchool->getType());
    }

    public function testLoadFromFileThrowsWhenFileMissing(): void
    {
        $loader = new SchoolLoader();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/nie istnieje!!!/');

        $loader->loadFromFile('/path/that/does/not/exist.txt');
    }
}
