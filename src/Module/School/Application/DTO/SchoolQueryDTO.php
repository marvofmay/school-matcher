<?php

declare(strict_types=1);

namespace App\Module\School\Application\DTO;

use App\Module\School\Application\Interface\QueryDTOInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class SchoolQueryDTO implements QueryDTOInterface
{
    #[Assert\NotBlank(message: 'The "name" field cannot be blank')]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: 'The "name" must be at least {{ limit }} characters long',
        maxMessage: 'The "name" cannot exceed {{ limit }} characters'
    )]
    public ?string $name = null;

    #[Assert\Length(
        max: 100,
        maxMessage: 'The city name cannot exceed {{ limit }} characters'
    )]
    public ?string $city = null;

    #[Assert\Length(
        max: 50,
        maxMessage: 'The school type cannot exceed {{ limit }} characters'
    )]
    public ?string $type = null;
}
