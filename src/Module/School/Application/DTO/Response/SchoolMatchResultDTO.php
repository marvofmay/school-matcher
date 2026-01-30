<?php

declare(strict_types=1);

namespace App\Module\School\Application\DTO\Response;

final readonly class SchoolMatchResultDTO
{
    public function __construct(
        public string $schoolName,
        public int $score,
    ) {
    }
}
