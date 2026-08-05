<?php

declare(strict_types=1);

namespace Theatrical;

use Theatrical\Exception\UnknownPlayTypeException;

/**
 * Computes the billed amount and volume credits for a single performance.
 *
 * Concrete subclasses encode the pricing rules for one play genre, so
 * adding a new genre means adding one class rather than editing a
 * central switch statement in two places.
 */
abstract class PerformanceCalculator
{
    private const CREDIT_AUDIENCE_THRESHOLD = 30;

    public function __construct(
        protected Performance $performance,
        protected Play $play
    ) {
    }

    public static function create(Performance $performance, Play $play): self
    {
        return match ($play->type) {
            'tragedy' => new TragedyCalculator($performance, $play),
            'comedy' => new ComedyCalculator($performance, $play),
            default => throw new UnknownPlayTypeException($play->type),
        };
    }

    abstract public function amount(): Money;

    public function volumeCredits(): int
    {
        return max($this->performance->audience - self::CREDIT_AUDIENCE_THRESHOLD, 0);
    }
}
