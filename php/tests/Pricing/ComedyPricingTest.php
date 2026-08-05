<?php

declare(strict_types=1);

namespace Tests\Pricing;

use PHPUnit\Framework\TestCase;
use Theatrical\Pricing\ComedyPricing;

final class ComedyPricingTest extends TestCase
{
    public function testExposesConstructedValues(): void
    {
        $pricing = new ComedyPricing(
            baseAmountCents: 30000,
            centsPerAttendee: 300,
            audienceBonusThreshold: 20,
            bonusFlatCents: 10000,
            bonusCentsPerAttendee: 500,
            attendeesPerVolumeCredit: 5
        );

        $this->assertSame(30000, $pricing->baseAmountCents);
        $this->assertSame(300, $pricing->centsPerAttendee);
        $this->assertSame(20, $pricing->audienceBonusThreshold);
        $this->assertSame(10000, $pricing->bonusFlatCents);
        $this->assertSame(500, $pricing->bonusCentsPerAttendee);
        $this->assertSame(5, $pricing->attendeesPerVolumeCredit);
    }
}
