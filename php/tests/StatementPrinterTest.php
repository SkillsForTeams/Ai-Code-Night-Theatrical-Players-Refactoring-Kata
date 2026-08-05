<?php

declare(strict_types=1);

namespace Tests;

use ApprovalTests\Approvals;
use Error;
use PHPUnit\Framework\TestCase;
use Theatrical\Invoice;
use Theatrical\Performance;
use Theatrical\Play;
use Theatrical\Pricing\PricingConfig;
use Theatrical\StatementPrinter;

final class StatementPrinterTest extends TestCase
{
    public function testCanPrintInvoice(): void
    {
        $plays = [
            'hamlet' => new Play('Hamlet', 'tragedy'),
            'as-like' => new Play('As You Like It', 'comedy'),
            'othello' => new Play('Othello', 'tragedy'),
        ];

        $performances = [
            new Performance('hamlet', 55),
            new Performance('as-like', 35),
            new Performance('othello', 40),
        ];

        Approvals::verifyString($this->printStatement($plays, $performances));
    }

    public function testTragedyWithNoAudienceBonus(): void
    {
        $plays = [
            'hamlet' => new Play('Hamlet', 'tragedy'),
        ];
        $performances = [new Performance('hamlet', 25)]; // <= 30, no bonus
        $result = $this->printStatement($plays, $performances);

        // 25 attendees, no bonus over 30 -> flat $400.00, 0 volume credits
        $this->assertStringContainsString('Hamlet: $400.00 (25 seats)', $result);
        $this->assertStringContainsString('Amount owed is $400.00', $result);
        $this->assertStringContainsString('You earned 0 credits', $result);
    }

    public function testComedyWithNoAudienceBonus(): void
    {
        $plays = [
            'as-like' => new Play('As You Like It', 'comedy'),
        ];
        $performances = [new Performance('as-like', 15)]; // <= 20, no bonus
        $result = $this->printStatement($plays, $performances);

        // 15 attendees, no bonus over 20 -> $300 base + $3*15 = $345.00
        // volume credits: max(15-30, 0) + floor(15/5) = 0 + 3 = 3
        $this->assertStringContainsString('As You Like It: $345.00 (15 seats)', $result);
        $this->assertStringContainsString('Amount owed is $345.00', $result);
        $this->assertStringContainsString('You earned 3 credits', $result);
    }

    public function testTragedyAudienceExactlyAtThreshold(): void
    {
        $plays = [
            'hamlet' => new Play('Hamlet', 'tragedy'),
        ];
        $performances = [new Performance('hamlet', 30)]; // == 30, condition is > 30, so no bonus yet
        $result = $this->printStatement($plays, $performances);

        // 30 attendees, boundary not crossed -> flat $400.00, 0 volume credits
        $this->assertStringContainsString('Hamlet: $400.00 (30 seats)', $result);
        $this->assertStringContainsString('Amount owed is $400.00', $result);
        $this->assertStringContainsString('You earned 0 credits', $result);
    }

    public function testComedyAudienceExactlyAtThreshold(): void
    {
        $plays = [
            'as-like' => new Play('As You Like It', 'comedy'),
        ];
        $performances = [new Performance('as-like', 20)]; // == 20, condition is > 20, so no bonus yet
        $result = $this->printStatement($plays, $performances);

        // 20 attendees, boundary not crossed -> $300 base + $3*20 = $360.00
        // volume credits: max(20-30, 0) + floor(20/5) = 0 + 4 = 4
        $this->assertStringContainsString('As You Like It: $360.00 (20 seats)', $result);
        $this->assertStringContainsString('Amount owed is $360.00', $result);
        $this->assertStringContainsString('You earned 4 credits', $result);
    }

    public function testCustomPricingConfigChangesAmountsAndCurrency(): void
    {
        $plays = [
            'hamlet' => new Play('Hamlet', 'tragedy'),
        ];
        $performances = [new Performance('hamlet', 20)]; // <= 30, no bonus
        $pricingConfig = PricingConfig::fromArray([
            'currency' => [
                'locale' => 'en_US',
                'code' => 'EUR',
            ],
            'genres' => [
                'tragedy' => [
                    'baseAmountCents' => 50000, // $500 base instead of $400
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

        $result = $this->printStatement($plays, $performances, 'BigCo', $pricingConfig);

        // Config-driven base amount ($500) and currency (EUR) both take effect.
        $this->assertStringContainsString('Hamlet: €500.00 (20 seats)', $result);
        $this->assertStringContainsString('Amount owed is €500.00', $result);
    }

    public function testNewPlayTypes(): void
    {
        $plays = [
            'henry-v' => new Play('Henry V', 'history'),
            'as-like' => new Play('As You Like It', 'comedy'),
        ];

        $performances = [new Performance('henry-v', 53), new Performance('as-like', 55)];

        $this->expectException(Error::class);
        $this->printStatement($plays, $performances);
    }

    /**
     * @param array<string, Play> $plays
     * @param array<int, Performance> $performances
     */
    private function printStatement(
        array $plays,
        array $performances,
        string $customer = 'BigCo',
        ?PricingConfig $pricingConfig = null
    ): string {
        $invoice = new Invoice($customer, $performances);
        return (new StatementPrinter($pricingConfig))->print($invoice, $plays);
    }
}
