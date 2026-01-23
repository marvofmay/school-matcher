<?php

declare(strict_types=1);

namespace App\Module\School\Domain\ValueObject;

final readonly class SchoolName
{
    private function __construct(private string $name)
    {
    }

    public static function create(string $name): self
    {
        $name = trim($name);
        if ($name === '') {
            throw new \InvalidArgumentException('School name cannot be empty');
        }

        return new self($name);
    }

    public function getValue(): string
    {
        return $this->name;
    }

    public function score(string $input): int
    {
        $input = $this->normalize($input);
        $name  = $this->normalize($this->name);

        if ($input === '') {
            return 0;
        }

        if ($input === $name) {
            return 100;
        }

        if (str_contains($name, $input) || str_contains($input, $name)) {
            return 80;
        }

        $distance = levenshtein($input, $name);
        $maxLen = max(strlen($input), strlen($name));
        $similarity = 1 - ($distance / $maxLen);
        return (int) round($similarity * 100);
    }

    private function normalize(string $value): string
    {
        $value = mb_strtolower($value);
        $value = iconv('UTF-8', 'ASCII//TRANSLIT', $value);
        $value = preg_replace('/[^a-z0-9 ]/', '', $value);
        $value = preg_replace('/\s+/', ' ', $value);

        return trim($value);
    }
}
