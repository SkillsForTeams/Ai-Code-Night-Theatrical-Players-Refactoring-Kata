import {
  EnrichedPerformance,
  Invoice,
  Plays,
  StatementData,
} from "./domain";
import { createPerformanceCalculator } from "./performanceCalculator";

export function createStatementData(
  invoice: Invoice,
  plays: Plays
): StatementData {
  const performances = invoice.performances.map(
    (performance): EnrichedPerformance => {
      const play = plays[performance.playID];
      const calculator = createPerformanceCalculator(performance, play);

      return {
        ...performance,
        play,
        amount: calculator.amount,
        volumeCredits: calculator.volumeCredits,
      };
    }
  );

  return {
    customer: invoice.customer,
    performances,
    totalAmount: performances.reduce(
      (total, performance) => total + performance.amount,
      0
    ),
    totalVolumeCredits: performances.reduce(
      (total, performance) => total + performance.volumeCredits,
      0
    ),
  };
}
