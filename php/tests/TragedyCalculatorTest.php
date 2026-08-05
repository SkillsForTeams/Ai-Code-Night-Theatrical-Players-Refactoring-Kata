<?php

declare(strict_types=1);

namespace Tests;

use NumberFormatter;
use PHPUnit\Framework\TestCase;
use Theatrical\Performance;
use Theatrical\Play;
use Theatrical\Pricing\TragedyPricing;
use Theatrical\TragedyCalculator;

final class TragedyCalculatorTest extends TestCase
{
    private NumberFormatter $formatter;

    private TragedyPricing $pricing;

    protected function setUp(): void
    {
        $this->formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
        $this->pricing = new TragedyPricing(
            baseAmountCents: 40000,
            audienceBonusThreshold: 30,
            bonusCentsPerAttendee: 1000
        );
    }

    public function testAmountIsFlatBaseAtThreshold(): void
    {
        $calculator = $this->makeCalculator(30);

        $this->assertSame('$400.00', $calculator->amount()->format($this->formatter, 'USD'));
    }

    public function testAmountAddsBonusPerAttendeeAboveThreshold(): void
    {
        $calculator = $this->makeCalculator(55);

        // $400 base + $10 * (55 - 30) = $400 + $250 = $650
        $this->assertSame('$650.00', $calculator->amount()->format($this->formatter, 'USD'));
    }

    public function testVolumeCreditsIsZeroAtThreshold(): void
    {
        $calculator = $this->makeCalculator(30);

        $this->assertSame(0, $calculator->volumeCredits());
    }

    public function testVolumeCreditsIsOnePerAttendeeAboveThreshold(): void
    {
        $calculator = $this->makeCalculator(55);

        $this->assertSame(25, $calculator->volumeCredits());
    }

    public function testConfiguredAmountsAreUsedInsteadOfHardcodedNumbers(): void
    {
        $pricing = new TragedyPricing(
            baseAmountCents: 100000,
            audienceBonusThreshold: 10,
            bonusCentsPerAttendee: 2000
        );
        $calculator = new TragedyCalculator(
            new Performance('hamlet', 15),
            new Play('Hamlet', 'tragedy'),
            30,
            $pricing
        );

        // $1000 base + $20 * (15 - 10) = $1000 + $100 = $1100
        $this->assertSame('$1,100.00', $calculator->amount()->format($this->formatter, 'USD'));
    }

    private function makeCalculator(int $audience, int $creditAudienceThreshold = 30): TragedyCalculator
    {
        return new TragedyCalculator(
            new Performance('hamlet', $audience),
            new Play('Hamlet', 'tragedy'),
            $creditAudienceThreshold,
            $this->pricing
        );
    }
}
