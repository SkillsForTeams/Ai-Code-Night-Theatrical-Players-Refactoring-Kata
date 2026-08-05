<?php

declare(strict_types=1);

namespace Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Theatrical\Play;

final class PlayTest extends TestCase
{
    public function testToStringFormatsNameAndType(): void
    {
        $play = new Play('Hamlet', 'tragedy');

        $this->assertSame('Hamlet : tragedy', (string) $play);
    }

    public function testConstructorRejectsEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Play('', 'tragedy');
    }

    public function testConstructorRejectsEmptyType(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Play('Hamlet', '');
    }
}
