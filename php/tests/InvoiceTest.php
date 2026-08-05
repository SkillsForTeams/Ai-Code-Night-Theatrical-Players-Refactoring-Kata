<?php

declare(strict_types=1);

namespace Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Theatrical\Invoice;
use Theatrical\Performance;

final class InvoiceTest extends TestCase
{
    public function testConstructorAcceptsValidValues(): void
    {
        $performances = [new Performance('hamlet', 55)];
        $invoice = new Invoice('BigCo', $performances);

        $this->assertSame('BigCo', $invoice->customer);
        $this->assertSame($performances, $invoice->performances);
    }

    public function testConstructorRejectsEmptyCustomer(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Invoice('', []);
    }
}
