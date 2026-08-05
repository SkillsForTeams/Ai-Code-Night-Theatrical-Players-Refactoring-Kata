<?php

declare(strict_types=1);

namespace Theatrical;

use InvalidArgumentException;

class Play implements \Stringable
{
    public function __construct(
        public string $name,
        public string $type
    ) {
        if (trim($name) === '') {
            throw new InvalidArgumentException('Play name must not be empty.');
        }

        if (trim($type) === '') {
            throw new InvalidArgumentException('Play type must not be empty.');
        }
    }

    public function __toString(): string
    {
        return (string) $this->name . ' : ' . $this->type;
    }
}
