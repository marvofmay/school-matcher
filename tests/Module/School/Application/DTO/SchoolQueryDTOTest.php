<?php

declare(strict_types=1);

namespace App\Tests\Module\School\Application\DTO;

use App\Module\School\Application\DTO\Request\SchoolQueryDTO;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class SchoolQueryDTOTest extends KernelTestCase
{
    private function getValidator(): ValidatorInterface
    {
        self::bootKernel();
        return static::getContainer()->get('validator');
    }

    public function testValidDTO(): void
    {
        $dto = new SchoolQueryDTO();
        $dto->name = 'I Liceum Ogólnokształcące';

        $validator = $this->getValidator();
        $errors = $validator->validate($dto);

        $this->assertCount(0, $errors);
    }

    public function testBlankName(): void
    {
        $dto = new SchoolQueryDTO();
        $dto->name = '';

        $validator = $this->getValidator();
        $errors = $validator->validate($dto);

        $this->assertGreaterThan(0, count($errors));
        $this->assertStringContainsString('cannot be blank', (string)$errors);
    }

    public function testNameTooShort(): void
    {
        $dto = new SchoolQueryDTO();
        $dto->name = 'AB';

        $validator = $this->getValidator();
        $errors = $validator->validate($dto);

        $this->assertGreaterThan(0, count($errors));
        $this->assertStringContainsString('at least 3 characters', (string)$errors);
    }

    public function testCityTooLong(): void
    {
        $dto = new SchoolQueryDTO();
        $dto->name = 'Valid Name';
        $dto->city = str_repeat('A', 101);

        $validator = $this->getValidator();
        $errors = $validator->validate($dto);

        $this->assertGreaterThan(0, count($errors));
        $this->assertStringContainsString('cannot exceed 100 characters', (string)$errors);
    }
}
