<?php

declare(strict_types=1);

namespace App\Module\School\Application\Interface;

interface QueryHandlerInterface
{
    public function __invoke(QueryInterface $query): mixed;
}
