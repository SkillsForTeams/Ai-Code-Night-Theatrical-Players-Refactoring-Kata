<?php

declare(strict_types=1);

namespace Theatrical;

use InvalidArgumentException;

class Invoice
{
    /**
     * @param array<int, Performance> $performances
     */
    public function __construct(
        public string $customer,
        public array $performances
    ) {
        if (trim($customer) === '') {
            throw new InvalidArgumentException('Invoice customer must not be empty.');
        }
    }
}
