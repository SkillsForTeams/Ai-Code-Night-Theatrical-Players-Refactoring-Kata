<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Theatrical\ComedyCalculator;
use Theatrical\Exception\UnknownPlayTypeException;
use Theatrical\Performance;
use Theatrical\PerformanceCalculator;
use Theatrical\Play;
use Theatrical\Pricing\PricingConfig;
use Theatrical\TragedyCalculator;

final class PerformanceCalculatorTest extends TestCase
{
    public function testCreateReturnsTragedyCalculatorForTragedyGenre(): void
    {
        $calculator = PerformanceCalculator::create(
            new Performance('hamlet', 55),
            new Play('Hamlet', 'tragedy'),
            $this->pricingConfig()
        );

        $this->assertInstanceOf(TragedyCalculator::class, $calculator);
    }

    public function testCreateReturnsComedyCalculatorForComedyGenre(): void
    {
        $calculator = PerformanceCalculator::create(
            new Performance('as-like', 35),
            new Play('As You Like It', 'comedy'),
            $this->pricingConfig()
        );

        $this->assertInstanceOf(ComedyCalculator::class, $calculator);
    }

    public function testCreateThrowsForUnknownGenre(): void
    {
        $this->expectException(UnknownPlayTypeException::class);
        $this->expectExceptionMessage('Unknown type: history');

        PerformanceCalculator::create(
            new Performance('henry-v', 53),
            new Play('Henry V', 'history'),
            $this->pricingConfig()
        );
    }

    public function testVolumeCreditsUsesConfiguredThreshold(): void
    {
        $calculator = PerformanceCalculator::create(
            new Performance('hamlet', 40),
            new Play('Hamlet', 'tragedy'),
            $this->pricingConfig()
        );

        // configured threshold is 30 -> max(40-30, 0) = 10
        $this->assertSame(10, $calculator->volumeCredits());
    }

    private function pricingConfig(): PricingConfig
    {
        return PricingConfig::fromArray([
            'currency' => [
                'locale' => 'en_US',
                'code' => 'USD',
            ],
            'genres' => [
                'tragedy' => [
                    'baseAmountCents' => 40000,
                    'audienceBonusThreshold' => 30,
                    'bonusCentsPerAttendee' => 1000,
                ],
                'comedy' => [
                    'baseAmountCents' => 30000,
                    'centsPerAttendee' => 300,
                    'audienceBonusThreshold' => 20,
                    'bonusFlatCents' => 10000,
                    'bonusCentsPerAttendee' => 500,
                    'attendeesPerVolumeCredit' => 5,
                ],
            ],
            'volumeCredits' => [
                'creditAudienceThreshold' => 30,
            ],
        ]);
    }
}
