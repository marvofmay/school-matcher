<?php

declare(strict_types=1);

namespace App\Module\School\Domain\ValueObject;

use App\Module\School\Domain\Entity\School;

final readonly class SchoolMatch
{
    public function __construct(
        private School $school,
        private MatchScore $score,
    ) {
    }

    public function school(): School
    {
        return $this->school;
    }

    public function score(): MatchScore
    {
        return $this->score;
    }
}
