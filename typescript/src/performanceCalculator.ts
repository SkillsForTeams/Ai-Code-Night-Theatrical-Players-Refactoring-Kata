import { statementConfig } from "./config";
import { Performance, Play } from "./domain";

abstract class PerformanceCalculator {
  constructor(
    readonly performance: Performance,
    readonly play: Play
  ) {}

  abstract get amount(): number;

  get volumeCredits(): number {
    return Math.max(this.performance.audience - 30, 0);
  }
}

class TragedyCalculator extends PerformanceCalculator {
  get amount(): number {
    const pricing = statementConfig.pricing.tragedy;
    let result = pricing.baseAmount;
    if (this.performance.audience > pricing.audienceThreshold) {
      result +=
        pricing.amountPerAudienceAboveThreshold *
        (this.performance.audience - pricing.audienceThreshold);
    }
    return result;
  }
}

class ComedyCalculator extends PerformanceCalculator {
  get amount(): number {
    const pricing = statementConfig.pricing.comedy;
    let result = pricing.baseAmount;
    if (this.performance.audience > pricing.audienceThreshold) {
      result +=
        pricing.amountAboveThreshold +
        pricing.amountPerAudienceAboveThreshold *
          (this.performance.audience - pricing.audienceThreshold);
    }
    return result + pricing.amountPerAudience * this.performance.audience;
  }

  get volumeCredits(): number {
    return (
      super.volumeCredits + Math.floor(this.performance.audience / 5)
    );
  }
}

export function createPerformanceCalculator(
  performance: Performance,
  play: Play
): PerformanceCalculator {
  switch (play.type) {
    case "tragedy":
      return new TragedyCalculator(performance, play);
    case "comedy":
      return new ComedyCalculator(performance, play);
    default:
      throw new Error(`unknown type: ${play.type}`);
  }
}
