<?php

declare(strict_types=1);

namespace App\Tests\Module\School\Application\Query;

use App\Module\School\Application\DTO\Request\SchoolQueryDTO;
use App\Module\School\Application\Query\SchoolMatcherQuery;
use PHPUnit\Framework\TestCase;

final class SchoolMatcherQueryTest extends TestCase
{
    public function testQueryStoresDTO(): void
    {
        $dto = new SchoolQueryDTO();
        $dto->name = 'I Liceum Ogólnokształcące';

        $query = new SchoolMatcherQuery($dto);

        $this->assertSame($dto, $query->queryDTO);
    }
}
