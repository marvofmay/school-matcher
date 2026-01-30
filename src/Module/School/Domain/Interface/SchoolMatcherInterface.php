<?php

declare(strict_types=1);

namespace App\Module\School\Domain\Interface;

use App\Module\School\Domain\ValueObject\SchoolMatch;

interface SchoolMatcherInterface
{
    /**
     * @return SchoolMatch[]
     */
    public function match(string $name, ?string $city = null, ?string $type = null): array;
}
