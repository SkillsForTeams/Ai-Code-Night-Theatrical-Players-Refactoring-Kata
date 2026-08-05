import { StatementData } from "./domain";

const formatCurrency = new Intl.NumberFormat("en-US", {
  style: "currency",
  currency: "USD",
  minimumFractionDigits: 2,
}).format;

export function renderText(data: StatementData): string {
  let result = `Statement for ${data.customer}\n`;

  for (const performance of data.performances) {
    result += ` ${performance.play.name}: ${formatCurrency(
      performance.amount / 100
    )} (${performance.audience} seats)\n`;
  }

  result += `Amount owed is ${formatCurrency(data.totalAmount / 100)}\n`;
  result += `You earned ${data.totalVolumeCredits} credits\n`;
  return result;
}
