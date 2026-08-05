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
    let result = 40000;
    if (this.performance.audience > 30) {
      result += 1000 * (this.performance.audience - 30);
    }
    return result;
  }
}

class ComedyCalculator extends PerformanceCalculator {
  get amount(): number {
    let result = 30000;
    if (this.performance.audience > 20) {
      result += 10000 + 500 * (this.performance.audience - 20);
    }
    return result + 300 * this.performance.audience;
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
