<?php

declare(strict_types=1);

namespace Theatrical;

use Theatrical\Exception\UnknownPlayTypeException;
use Theatrical\Pricing\PricingConfig;

/**
 * Computes the billed amount and volume credits for a single performance.
 *
 * Concrete subclasses encode the pricing rules for one play genre, so
 * adding a new genre means adding one class rather than editing a
 * central conditional in two places. The actual amounts and thresholds
 * come from PricingConfig, so they can be changed without touching code.
 */
abstract class PerformanceCalculator
{
    public function __construct(
        protected Performance $performance,
        protected Play $play,
        private int $creditAudienceThreshold
    ) {
    }

    public static function create(Performance $performance, Play $play, PricingConfig $pricingConfig): self
    {
        return match ($play->type) {
            'tragedy' => new TragedyCalculator(
                $performance,
                $play,
                $pricingConfig->creditAudienceThreshold(),
                $pricingConfig->tragedyPricing()
            ),
            'comedy' => new ComedyCalculator(
                $performance,
                $play,
                $pricingConfig->creditAudienceThreshold(),
                $pricingConfig->comedyPricing()
            ),
            default => throw new UnknownPlayTypeException($play->type),
        };
    }

    abstract public function amount(): Money;

    public function volumeCredits(): int
    {
        return max($this->performance->audience - $this->creditAudienceThreshold, 0);
    }
}
