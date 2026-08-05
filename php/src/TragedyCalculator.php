<?php

declare(strict_types=1);

namespace Theatrical;

use Theatrical\Pricing\TragedyPricing;

final class TragedyCalculator extends PerformanceCalculator
{
    public function __construct(
        Performance $performance,
        Play $play,
        int $creditAudienceThreshold,
        private TragedyPricing $pricing
    ) {
        parent::__construct($performance, $play, $creditAudienceThreshold);
    }

    public function amount(): Money
    {
        $cents = $this->pricing->baseAmountCents;

        if ($this->performance->audience > $this->pricing->audienceBonusThreshold) {
            $cents += $this->pricing->bonusCentsPerAttendee
                * ($this->performance->audience - $this->pricing->audienceBonusThreshold);
        }

        return Money::fromCents($cents);
    }
}
