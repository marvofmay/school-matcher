<?php

declare(strict_types=1);

namespace App\Module\School\Application\QueryHandler;

use App\Module\School\Application\Interface\QueryHandlerInterface;
use App\Module\School\Application\Interface\QueryInterface;
use App\Module\School\Application\Query\SchoolMatcherQuery;
use App\Module\School\Domain\Service\SchoolMatcher;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class SchoolMatcherQueryHandler implements QueryHandlerInterface
{
    public function __construct(private SchoolMatcher $matcher)
    {
    }

    /**
     * @param SchoolMatcherQuery $query
     * @return array<int, array{school: string, score: int}>
     */
    public function __invoke(QueryInterface $query): array
    {
        return $this->matcher->match($query->queryDTO->name, $query->queryDTO->city, $query->queryDTO->type);
    }
}
{

}
