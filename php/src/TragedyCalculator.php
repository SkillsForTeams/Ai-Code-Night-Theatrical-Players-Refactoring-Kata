<?php

declare(strict_types=1);

namespace Theatrical;

final class TragedyCalculator extends PerformanceCalculator
{
    private const BASE_AMOUNT_CENTS = 40000;

    private const AUDIENCE_BONUS_THRESHOLD = 30;

    private const BONUS_CENTS_PER_ATTENDEE = 1000;

    public function amount(): Money
    {
        $cents = self::BASE_AMOUNT_CENTS;

        if ($this->performance->audience > self::AUDIENCE_BONUS_THRESHOLD) {
            $cents += self::BONUS_CENTS_PER_ATTENDEE
                * ($this->performance->audience - self::AUDIENCE_BONUS_THRESHOLD);
        }

        return Money::fromCents($cents);
    }
}
