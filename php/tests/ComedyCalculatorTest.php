<?php

declare(strict_types=1);

namespace Tests;

use NumberFormatter;
use PHPUnit\Framework\TestCase;
use Theatrical\ComedyCalculator;
use Theatrical\Performance;
use Theatrical\Play;
use Theatrical\Pricing\ComedyPricing;

final class ComedyCalculatorTest extends TestCase
{
    private NumberFormatter $formatter;

    private ComedyPricing $pricing;

    protected function setUp(): void
    {
        $this->formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
        $this->pricing = new ComedyPricing(
            baseAmountCents: 30000,
            centsPerAttendee: 300,
            audienceBonusThreshold: 20,
            bonusFlatCents: 10000,
            bonusCentsPerAttendee: 500,
            attendeesPerVolumeCredit: 5
        );
    }

    public function testAmountHasNoBonusAtThreshold(): void
    {
        $calculator = $this->makeCalculator(20);

        // $300 base + $3 * 20 attendee = $300 + $60 = $360, no bonus (20 is not > 20)
        $this->assertSame('$360.00', $calculator->amount()->format($this->formatter, 'USD'));
    }

    public function testAmountAddsBonusAboveThreshold(): void
    {
        $calculator = $this->makeCalculator(35);

        // base $300 + bonus ($100 + $5 * (35-20) = $175) + attendee $3 * 35 = $105 -> $580
        $this->assertSame('$580.00', $calculator->amount()->format($this->formatter, 'USD'));
    }

    public function testVolumeCreditsAddsOneCreditPerConfiguredAttendeeGroup(): void
    {
        $calculator = $this->makeCalculator(15);

        // max(15-30, 0) = 0, plus floor(15/5) = 3
        $this->assertSame(3, $calculator->volumeCredits());
    }

    public function testVolumeCreditsCombinesAudienceBonusAndAttendeeBonus(): void
    {
        $calculator = $this->makeCalculator(55);

        // max(55-30, 0) = 25, plus floor(55/5) = 11 -> 36
        $this->assertSame(36, $calculator->volumeCredits());
    }

    public function testConfiguredAttendeesPerVolumeCreditIsUsedInsteadOfHardcodedFive(): void
    {
        $pricing = new ComedyPricing(
            baseAmountCents: 30000,
            centsPerAttendee: 300,
            audienceBonusThreshold: 20,
            bonusFlatCents: 10000,
            bonusCentsPerAttendee: 500,
            attendeesPerVolumeCredit: 10
        );
        $calculator = new ComedyCalculator(
            new Performance('as-like', 20),
            new Play('As You Like It', 'comedy'),
            30,
            $pricing
        );

        // max(20-30, 0) = 0, plus floor(20/10) = 2
        $this->assertSame(2, $calculator->volumeCredits());
    }

    private function makeCalculator(int $audience, int $creditAudienceThreshold = 30): ComedyCalculator
    {
        return new ComedyCalculator(
            new Performance('as-like', $audience),
            new Play('As You Like It', 'comedy'),
            $creditAudienceThreshold,
            $this->pricing
        );
    }
}
