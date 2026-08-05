<?php

declare(strict_types=1);

namespace Theatrical;

use InvalidArgumentException;

class Performance
{
    public function __construct(
        public string $playId,
        public int $audience
    ) {
        if (trim($playId) === '') {
            throw new InvalidArgumentException('Performance playId must not be empty.');
        }

        if ($audience < 0) {
            throw new InvalidArgumentException('Performance audience must not be negative.');
        }
    }
}
