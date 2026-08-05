<?php

declare(strict_types=1);

namespace Tests\Pricing;

use PHPUnit\Framework\TestCase;
use Theatrical\Pricing\TragedyPricing;

final class TragedyPricingTest extends TestCase
{
    public function testExposesConstructedValues(): void
    {
        $pricing = new TragedyPricing(
            baseAmountCents: 40000,
            audienceBonusThreshold: 30,
            bonusCentsPerAttendee: 1000
        );

        $this->assertSame(40000, $pricing->baseAmountCents);
        $this->assertSame(30, $pricing->audienceBonusThreshold);
        $this->assertSame(1000, $pricing->bonusCentsPerAttendee);
    }
}
