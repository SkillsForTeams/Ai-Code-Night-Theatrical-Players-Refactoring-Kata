<?php

declare(strict_types=1);

namespace Tests;

use NumberFormatter;
use PHPUnit\Framework\TestCase;
use Theatrical\Money;

final class MoneyTest extends TestCase
{
    private NumberFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
    }

    public function testFromCentsFormatsAsDecimalCurrency(): void
    {
        $money = Money::fromCents(150050);

        $this->assertSame('$1,500.50', $money->format($this->formatter, 'USD'));
    }

    public function testFormatUsesGivenCurrencyCode(): void
    {
        $money = Money::fromCents(50000);

        $this->assertSame('€500.00', $money->format($this->formatter, 'EUR'));
    }

    public function testAddSumsCents(): void
    {
        $sum = Money::fromCents(100)->add(Money::fromCents(250));

        $this->assertSame('$3.50', $sum->format($this->formatter, 'USD'));
    }

    public function testAddDoesNotMutateOperands(): void
    {
        $a = Money::fromCents(100);
        $b = Money::fromCents(200);
        $sum = $a->add($b);

        $this->assertSame('$1.00', $a->format($this->formatter, 'USD'));
        $this->assertSame('$2.00', $b->format($this->formatter, 'USD'));
        $this->assertSame('$3.00', $sum->format($this->formatter, 'USD'));
    }

    public function testFromCentsZeroFormatsAsZero(): void
    {
        $this->assertSame('$0.00', Money::fromCents(0)->format($this->formatter, 'USD'));
    }
}
