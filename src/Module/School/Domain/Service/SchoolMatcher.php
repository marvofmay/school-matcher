<?php

declare(strict_types=1);

namespace App\Module\School\Domain\Service;

use App\Module\School\Application\Interface\SchoolMatcherInterface;
use App\Module\School\Domain\Entity\School;
use App\Module\School\Domain\ValueObject\MatchScore;

final readonly class SchoolMatcher implements SchoolMatcherInterface
{
    /**
     * @param School[] $schools
     */
    public function __construct(private array $schools = [])
    {
    }

    public function match(string $name, ?string $city = null, ?string $type = null): array
    {
        $results = [];

        foreach ($this->schools as $school) {
            if ($city && mb_strtolower($school->getCity()) !== mb_strtolower($city)) {
                continue;
            }

            if ($type && mb_strtolower($school->getType()) !== mb_strtolower($type)) {
                continue;
            }

            $best = 0;
            foreach ($school->getAllNames() as $schoolName) {
                $best = max($best, $schoolName->score($name));
            }

            $score = new MatchScore($best);
            if ($score->isAcceptable()) {
                $results[] = [
                    'school' => $school->getOfficialName()->getValue(),
                    'score'  => $score->value(),
                ];
            }
        }

        usort($results, fn ($a, $b) => $b['score'] <=> $a['score']);

        return $results;
    }
}
