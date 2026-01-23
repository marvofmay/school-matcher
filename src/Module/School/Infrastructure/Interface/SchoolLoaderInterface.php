<?php

declare(strict_types=1);

namespace App\Module\School\Infrastructure\Interface;

use App\Module\School\Domain\Entity\School;

interface SchoolLoaderInterface
{
    /**
     * @param string $filePath
     * @return School[]
     */
    public function loadFromFile(string $filePath): array;
}
