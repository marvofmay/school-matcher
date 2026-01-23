<?php

declare(strict_types=1);

namespace App\Tests\Module\School\Domain\ValueObject;

use App\Module\School\Domain\ValueObject\MatchScore;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class MatchScoreTest extends TestCase
{
    #[DataProvider('validScoresProvider')]
    public function test_it_accepts_valid_score(int $value): void
    {
        $score = new MatchScore($value);
        self::assertSame($value, $score->value());
    }

    public function test_score_is_acceptable_when_equal_or_above_threshold(): void
    {
        self::assertTrue(new MatchScore(70)->isAcceptable());
        self::assertTrue(new MatchScore(85)->isAcceptable());
        self::assertTrue(new MatchScore(100)->isAcceptable());
    }

    public function test_score_is_not_acceptable_below_threshold(): void
    {
        self::assertFalse(new MatchScore(69)->isAcceptable());
        self::assertFalse(new MatchScore(0)->isAcceptable());
    }

    #[DataProvider('invalidScoresProvider')]
    public function test_it_throws_exception_for_invalid_score(int $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new MatchScore($value);
    }

    /**
     * @return array<string, array{0:int}>
     */
    public static function validScoresProvider(): array
    {
        return [
            'zero' => [0],
            'threshold' => [70],
            'max' => [100],
            'random' => [42],
        ];
    }

    /**
     * @return array<string, array{0:int}>
     */
    public static function invalidScoresProvider(): array
    {
        return [
            'below zero' => [-1],
            'above hundred' => [101],
            'way too high' => [999],
        ];
    }

    public function test_score_equal_threshold_is_acceptable(): void
    {
        self::assertTrue(new MatchScore(70)->isAcceptable());
    }

    public function test_score_just_below_threshold_is_not_acceptable(): void
    {
        self::assertFalse(new MatchScore(69)->isAcceptable());
    }

    public function test_value_returns_given_score(): void
    {
        $score = new MatchScore(42);
        self::assertSame(42, $score->value());
    }
}
