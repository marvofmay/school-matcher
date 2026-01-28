<?php

declare(strict_types=1);

namespace App\Module\School\Domain\Interface;

interface SchoolMatcherInterface
{
    /**
     * @param string      $name
     * @param string|null $city
     * @param string|null $type
     * @return array<int, array{school: string, score: int}>
     */
    public function match(string $name, ?string $city = null, ?string $type = null): array;
}
