<?php

declare(strict_types=1);

namespace Theatrical;

use Theatrical\Pricing\ComedyPricing;

final class ComedyCalculator extends PerformanceCalculator
{
    public function __construct(
        Performance $performance,
        Play $play,
        int $creditAudienceThreshold,
        private ComedyPricing $pricing
    ) {
        parent::__construct($performance, $play, $creditAudienceThreshold);
    }

    public function amount(): Money
    {
        $cents = $this->pricing->baseAmountCents;

        if ($this->performance->audience > $this->pricing->audienceBonusThreshold) {
            $cents += $this->pricing->bonusFlatCents
                + $this->pricing->bonusCentsPerAttendee
                * ($this->performance->audience - $this->pricing->audienceBonusThreshold);
        }

        $cents += $this->pricing->centsPerAttendee * $this->performance->audience;

        return Money::fromCents($cents);
    }

    public function volumeCredits(): int
    {
        return parent::volumeCredits() + intdiv($this->performance->audience, $this->pricing->attendeesPerVolumeCredit);
    }
}
