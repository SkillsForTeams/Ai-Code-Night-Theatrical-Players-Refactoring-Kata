<?php

declare(strict_types=1);

namespace Theatrical\Pricing;

use InvalidArgumentException;

/**
 * Loads the amounts and currency used to price performances from a JSON
 * config file, so they can be changed without touching code.
 */
final class PricingConfig
{
    private function __construct(
        private string $currencyLocale,
        private string $currencyCode,
        private TragedyPricing $tragedyPricing,
        private ComedyPricing $comedyPricing,
        private int $creditAudienceThreshold
    ) {
    }

    public static function fromFile(string $path): self
    {
        if (! is_file($path)) {
            throw new InvalidArgumentException("Pricing config file not found: {$path}");
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new InvalidArgumentException("Unable to read pricing config file: {$path}");
        }

        /** @var mixed $data */
        $data = json_decode($contents, true);

        if (! is_array($data)) {
            throw new InvalidArgumentException("Pricing config file is not valid JSON: {$path}");
        }

        return self::fromArray($data);
    }

    /**
     * @param array<mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $currency = self::requireArray($data, 'currency');
        $genres = self::requireArray($data, 'genres');
        $tragedy = self::requireArray($genres, 'tragedy');
        $comedy = self::requireArray($genres, 'comedy');
        $volumeCredits = self::requireArray($data, 'volumeCredits');

        return new self(
            self::requireString($currency, 'locale'),
            self::requireString($currency, 'code'),
            new TragedyPricing(
                self::requireInt($tragedy, 'baseAmountCents'),
                self::requireInt($tragedy, 'audienceBonusThreshold'),
                self::requireInt($tragedy, 'bonusCentsPerAttendee')
            ),
            new ComedyPricing(
                self::requireInt($comedy, 'baseAmountCents'),
                self::requireInt($comedy, 'centsPerAttendee'),
                self::requireInt($comedy, 'audienceBonusThreshold'),
                self::requireInt($comedy, 'bonusFlatCents'),
                self::requireInt($comedy, 'bonusCentsPerAttendee'),
                self::requireInt($comedy, 'attendeesPerVolumeCredit')
            ),
            self::requireInt($volumeCredits, 'creditAudienceThreshold')
        );
    }

    public function currencyLocale(): string
    {
        return $this->currencyLocale;
    }

    public function currencyCode(): string
    {
        return $this->currencyCode;
    }

    public function tragedyPricing(): TragedyPricing
    {
        return $this->tragedyPricing;
    }

    public function comedyPricing(): ComedyPricing
    {
        return $this->comedyPricing;
    }

    public function creditAudienceThreshold(): int
    {
        return $this->creditAudienceThreshold;
    }

    /**
     * @param array<mixed> $data
     * @return array<mixed>
     */
    private static function requireArray(array $data, string $key): array
    {
        if (! isset($data[$key]) || ! is_array($data[$key])) {
            throw new InvalidArgumentException("Pricing config missing or invalid section: {$key}");
        }

        return $data[$key];
    }

    /**
     * @param array<mixed> $data
     */
    private static function requireInt(array $data, string $key): int
    {
        if (! isset($data[$key]) || ! is_int($data[$key])) {
            throw new InvalidArgumentException("Pricing config missing or invalid integer: {$key}");
        }

        return $data[$key];
    }

    /**
     * @param array<mixed> $data
     */
    private static function requireString(array $data, string $key): string
    {
        if (! isset($data[$key]) || ! is_string($data[$key]) || trim($data[$key]) === '') {
            throw new InvalidArgumentException("Pricing config missing or invalid string: {$key}");
        }

        return $data[$key];
    }
}
