<?php

declare(strict_types=1);

namespace App\Module\School\Application\Query;

use App\Module\School\Application\DTO\Request\SchoolQueryDTO;
use App\Module\School\Application\Interface\QueryInterface;

final readonly class SchoolMatcherQuery implements QueryInterface
{
    public function __construct(
        public SchoolQueryDTO $queryDTO
    ) {
    }
}
