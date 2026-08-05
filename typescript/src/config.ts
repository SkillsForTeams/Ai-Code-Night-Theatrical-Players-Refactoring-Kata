export const statementConfig = {
  currency: {
    locale: "en-US",
    options: {
      style: "currency",
      currency: "USD",
      minimumFractionDigits: 2,
    } satisfies Intl.NumberFormatOptions,
    minorUnitsPerMajorUnit: 100,
  },
  pricing: {
    tragedy: {
      baseAmount: 40000,
      audienceThreshold: 30,
      amountPerAudienceAboveThreshold: 1000,
    },
    comedy: {
      baseAmount: 30000,
      audienceThreshold: 20,
      amountAboveThreshold: 10000,
      amountPerAudienceAboveThreshold: 500,
      amountPerAudience: 300,
    },
  },
} as const;
