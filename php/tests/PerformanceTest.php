<?php

declare(strict_types=1);

namespace Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Theatrical\Performance;

final class PerformanceTest extends TestCase
{
    public function testConstructorAcceptsValidValues(): void
    {
        $performance = new Performance('hamlet', 55);

        $this->assertSame('hamlet', $performance->playId);
        $this->assertSame(55, $performance->audience);
    }

    public function testConstructorRejectsEmptyPlayId(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Performance('', 55);
    }

    public function testConstructorRejectsNegativeAudience(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Performance('hamlet', -1);
    }
}
