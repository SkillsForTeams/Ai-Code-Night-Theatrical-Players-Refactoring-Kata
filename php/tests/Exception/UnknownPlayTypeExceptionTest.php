<?php

declare(strict_types=1);

namespace Tests\Exception;

use Error;
use PHPUnit\Framework\TestCase;
use Theatrical\Exception\UnknownPlayTypeException;

final class UnknownPlayTypeExceptionTest extends TestCase
{
    public function testIsAnError(): void
    {
        $exception = new UnknownPlayTypeException('history');

        $this->assertInstanceOf(Error::class, $exception);
    }

    public function testMessageIncludesTheUnknownType(): void
    {
        $exception = new UnknownPlayTypeException('history');

        $this->assertSame('Unknown type: history', $exception->getMessage());
    }
}
