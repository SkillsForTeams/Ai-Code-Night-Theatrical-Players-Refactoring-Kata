<?php

declare(strict_types=1);

namespace Theatrical;

use NumberFormatter;

/**
 * A monetary amount, held as integer cents so totals accumulate without
 * float rounding errors; conversion to a decimal currency string only
 * happens at format time.
 */
final class Money
{
    private function __construct(
        private int $cents
    ) {
    }

    public static function fromCents(int $cents): self
    {
        return new self($cents);
    }

    public function add(self $other): self
    {
        return new self($this->cents + $other->cents);
    }

    public function format(NumberFormatter $formatter): string
    {
        return (string) $formatter->formatCurrency($this->cents / 100, 'USD');
    }
}
