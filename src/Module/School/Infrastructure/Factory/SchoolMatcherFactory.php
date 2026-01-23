<?php

declare(strict_types=1);

namespace App\Module\School\Infrastructure\Factory;

use App\Module\School\Domain\Service\SchoolMatcher;
use App\Module\School\Infrastructure\Interface\SchoolLoaderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class SchoolMatcherFactory
{
    public function __construct(private SchoolLoaderInterface $loader, #[Autowire('%school.data_file%')] private string $schoolsFile)
    {
    }

    public function __invoke(): SchoolMatcher
    {
        return new SchoolMatcher($this->loader->loadFromFile($this->schoolsFile));
    }
}
