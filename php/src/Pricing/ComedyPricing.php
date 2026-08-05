<?php

declare(strict_types=1);

namespace Theatrical\Pricing;

final class ComedyPricing
{
    public function __construct(
        public int $baseAmountCents,
        public int $centsPerAttendee,
        public int $audienceBonusThreshold,
        public int $bonusFlatCents,
        public int $bonusCentsPerAttendee,
        public int $attendeesPerVolumeCredit
    ) {
    }
}
