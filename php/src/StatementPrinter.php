<?php

declare(strict_types=1);

namespace Theatrical;

use NumberFormatter;

class StatementPrinter
{
    /**
     * @param array<string, Play> $plays
     */
    public function print(Invoice $invoice, array $plays): string
    {
        $totalAmount = Money::fromCents(0);
        $volumeCredits = 0;
        $format = new NumberFormatter('en_US', NumberFormatter::CURRENCY);

        $result = "Statement for {$invoice->customer}\n";

        foreach ($invoice->performances as $performance) {
            $play = $plays[$performance->playId];
            $calculator = PerformanceCalculator::create($performance, $play);
            $amount = $calculator->amount();

            $result .= "  {$play->name}: {$amount->format($format)} ({$performance->audience} seats)\n";

            $totalAmount = $totalAmount->add($amount);
            $volumeCredits += $calculator->volumeCredits();
        }

        $result .= "Amount owed is {$totalAmount->format($format)}\n";
        $result .= "You earned {$volumeCredits} credits";

        return $result;
    }
}
