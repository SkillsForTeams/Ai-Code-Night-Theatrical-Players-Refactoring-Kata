<?php

declare(strict_types=1);

namespace Tests\Pricing;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Theatrical\Pricing\PricingConfig;

final class PricingConfigTest extends TestCase
{
    public function testFromArrayExposesConfiguredValues(): void
    {
        $config = PricingConfig::fromArray($this->validData());

        $this->assertSame('en_US', $config->currencyLocale());
        $this->assertSame('USD', $config->currencyCode());
        $this->assertSame(30, $config->creditAudienceThreshold());
        $this->assertSame(40000, $config->tragedyPricing()->baseAmountCents);
        $this->assertSame(30000, $config->comedyPricing()->baseAmountCents);
    }

    public function testFromFileLoadsTheProjectDefaultConfig(): void
    {
        $config = PricingConfig::fromFile(__DIR__ . '/../../config/pricing.json');

        $this->assertSame('USD', $config->currencyCode());
        $this->assertSame(40000, $config->tragedyPricing()->baseAmountCents);
    }

    public function testFromFileRejectsMissingFile(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PricingConfig::fromFile(__DIR__ . '/does-not-exist.json');
    }

    public function testFromFileRejectsInvalidJson(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'pricing');
        $this->assertNotFalse($path);
        file_put_contents($path, 'not valid json');

        try {
            $this->expectException(InvalidArgumentException::class);
            PricingConfig::fromFile($path);
        } finally {
            unlink($path);
        }
    }

    public function testFromArrayRejectsMissingSection(): void
    {
        $data = $this->validData();
        unset($data['currency']);

        $this->expectException(InvalidArgumentException::class);

        PricingConfig::fromArray($data);
    }

    public function testFromArrayRejectsMissingGenre(): void
    {
        $data = $this->validData();
        /** @var array<string, mixed> $genres */
        $genres = $data['genres'];
        unset($genres['comedy']);
        $data['genres'] = $genres;

        $this->expectException(InvalidArgumentException::class);

        PricingConfig::fromArray($data);
    }

    public function testFromArrayRejectsNonIntegerAmount(): void
    {
        $data = $this->validData();
        /** @var array<string, mixed> $genres */
        $genres = $data['genres'];
        /** @var array<string, mixed> $tragedy */
        $tragedy = $genres['tragedy'];
        $tragedy['baseAmountCents'] = '40000';
        $genres['tragedy'] = $tragedy;
        $data['genres'] = $genres;

        $this->expectException(InvalidArgumentException::class);

        PricingConfig::fromArray($data);
    }

    public function testFromArrayRejectsEmptyCurrencyCode(): void
    {
        $data = $this->validData();
        /** @var array<string, mixed> $currency */
        $currency = $data['currency'];
        $currency['code'] = '';
        $data['currency'] = $currency;

        $this->expectException(InvalidArgumentException::class);

        PricingConfig::fromArray($data);
    }

    /**
     * @return array<string, mixed>
     */
    private function validData(): array
    {
        return [
            'currency' => [
                'locale' => 'en_US',
                'code' => 'USD',
            ],
            'genres' => [
                'tragedy' => [
                    'baseAmountCents' => 40000,
                    'audienceBonusThreshold' => 30,
                    'bonusCentsPerAttendee' => 1000,
                ],
                'comedy' => [
                    'baseAmountCents' => 30000,
                    'centsPerAttendee' => 300,
                    'audienceBonusThreshold' => 20,
                    'bonusFlatCents' => 10000,
                    'bonusCentsPerAttendee' => 500,
                    'attendeesPerVolumeCredit' => 5,
                ],
            ],
            'volumeCredits' => [
                'creditAudienceThreshold' => 30,
            ],
        ];
    }
}
