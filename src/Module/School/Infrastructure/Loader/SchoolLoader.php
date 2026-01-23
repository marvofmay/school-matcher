<?php

declare(strict_types=1);

namespace App\Module\School\Infrastructure\Loader;

use App\Module\School\Domain\Entity\School;
use App\Module\School\Domain\ValueObject\SchoolName;
use App\Module\School\Infrastructure\Interface\SchoolLoaderInterface;

final class SchoolLoader implements SchoolLoaderInterface
{
    public function loadFromFile(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException("Plik $filePath nie istnieje!!!");
        }

        $schools = [];
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            if (str_starts_with($line, '#')) {
                continue;
            }

            [$official, $alts, $city, $type] = array_map('trim', explode('|', $line));
            $altNames = array_map(fn ($n) => SchoolName::create(trim($n)), explode(',', $alts));
            $schools[] = School::create(SchoolName::create($official), $altNames, $city, $type);
        }

        return $schools;
    }
}
