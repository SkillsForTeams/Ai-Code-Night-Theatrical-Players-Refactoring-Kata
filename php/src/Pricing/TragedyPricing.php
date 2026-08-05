<?php

declare(strict_types=1);

namespace Theatrical\Pricing;

final class TragedyPricing
{
    public function __construct(
        public int $baseAmountCents,
        public int $audienceBonusThreshold,
        public int $bonusCentsPerAttendee
    ) {
    }
}
