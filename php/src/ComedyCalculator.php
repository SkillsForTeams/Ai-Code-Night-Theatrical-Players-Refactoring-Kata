<?php

declare(strict_types=1);

namespace Theatrical;

final class ComedyCalculator extends PerformanceCalculator
{
    private const BASE_AMOUNT_CENTS = 30000;

    private const CENTS_PER_ATTENDEE = 300;

    private const AUDIENCE_BONUS_THRESHOLD = 20;

    private const BONUS_FLAT_CENTS = 10000;

    private const BONUS_CENTS_PER_ATTENDEE = 500;

    private const ATTENDEES_PER_VOLUME_CREDIT = 5;

    public function amount(): Money
    {
        $cents = self::BASE_AMOUNT_CENTS;

        if ($this->performance->audience > self::AUDIENCE_BONUS_THRESHOLD) {
            $cents += self::BONUS_FLAT_CENTS
                + self::BONUS_CENTS_PER_ATTENDEE * ($this->performance->audience - self::AUDIENCE_BONUS_THRESHOLD);
        }

        $cents += self::CENTS_PER_ATTENDEE * $this->performance->audience;

        return Money::fromCents($cents);
    }

    public function volumeCredits(): int
    {
        return parent::volumeCredits() + intdiv($this->performance->audience, self::ATTENDEES_PER_VOLUME_CREDIT);
    }
}
