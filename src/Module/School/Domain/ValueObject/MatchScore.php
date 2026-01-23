<?php

declare(strict_types=1);

namespace App\Module\School\Domain\ValueObject;

final readonly class MatchScore
{
    private const int MIN_ACCEPTABLE = 70;

    public function __construct(private int $value)
    {
        if ($value < 0 || $value > 100) {
            throw new \InvalidArgumentException('Match score must be between 0 and 100.');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function isAcceptable(): bool
    {
        return $this->value >= self::MIN_ACCEPTABLE;
    }
}
