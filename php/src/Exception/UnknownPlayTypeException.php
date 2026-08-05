<?php

declare(strict_types=1);

namespace Theatrical\Exception;

use Error;

/**
 * Thrown when a play's genre has no matching PerformanceCalculator.
 *
 * Extends the built-in Error (rather than Exception) so existing callers
 * catching \Error for this unhandled-genre case keep working unchanged.
 */
final class UnknownPlayTypeException extends Error
{
    public function __construct(string $type)
    {
        parent::__construct("Unknown type: {$type}");
    }
}
