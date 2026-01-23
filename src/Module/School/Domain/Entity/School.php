<?php

declare(strict_types=1);

namespace App\Module\School\Domain\Entity;

use App\Module\School\Domain\ValueObject\SchoolName;

final readonly class School
{
    private function __construct(
        private SchoolName $officialName,
        /** @var SchoolName[] */
        private array $alternativeNames,
        private string $city,
        private string $type
    ) {
    }

    /**
     * @param SchoolName[] $alternativeNames
     */
    public static function create(
        SchoolName $officialName,
        array $alternativeNames,
        string $city,
        string $type
    ): self {
        $city = trim($city);
        $type = trim($type);

        if ($city === '') {
            throw new \InvalidArgumentException('City cannot be empty');
        }
        if ($type === '') {
            throw new \InvalidArgumentException('Type cannot be empty');
        }

        $altNames = [];
        foreach ($alternativeNames as $name) {

            $altNames[] = $name;
        }

        return new self($officialName, $altNames, $city, $type);
    }

    /**
     * @return SchoolName
     */
    public function getOfficialName(): SchoolName
    {
        return $this->officialName;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return SchoolName[]
     */
    public function getAllNames(): array
    {
        return array_merge([$this->officialName], $this->alternativeNames);
    }
}
