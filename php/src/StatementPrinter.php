<?php

declare(strict_types=1);

namespace Theatrical;

use NumberFormatter;
use Theatrical\Pricing\PricingConfig;

class StatementPrinter
{
    private PricingConfig $pricingConfig;

    public function __construct(?PricingConfig $pricingConfig = null)
    {
        $this->pricingConfig = $pricingConfig ?? PricingConfig::fromFile(self::defaultConfigPath());
    }

    /**
     * @param array<string, Play> $plays
     */
    public function print(Invoice $invoice, array $plays): string
    {
        $totalAmount = Money::fromCents(0);
        $volumeCredits = 0;
        $format = new NumberFormatter($this->pricingConfig->currencyLocale(), NumberFormatter::CURRENCY);
        $currencyCode = $this->pricingConfig->currencyCode();

        $result = "Statement for {$invoice->customer}\n";

        foreach ($invoice->performances as $performance) {
            $play = $plays[$performance->playId];
            $calculator = PerformanceCalculator::create($performance, $play, $this->pricingConfig);
            $amount = $calculator->amount();

            $result .= "  {$play->name}: {$amount->format($format, $currencyCode)} ({$performance->audience} seats)\n";

            $totalAmount = $totalAmount->add($amount);
            $volumeCredits += $calculator->volumeCredits();
        }

        $result .= "Amount owed is {$totalAmount->format($format, $currencyCode)}\n";
        $result .= "You earned {$volumeCredits} credits";

        return $result;
    }

    private static function defaultConfigPath(): string
    {
        return __DIR__ . '/../config/pricing.json';
    }
}
