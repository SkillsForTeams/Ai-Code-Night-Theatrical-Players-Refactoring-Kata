import { statementConfig } from "./config";
import { StatementData } from "./domain";

const formatCurrency = new Intl.NumberFormat(
  statementConfig.currency.locale,
  statementConfig.currency.options
).format;

export function renderText(data: StatementData): string {
  let result = `Statement for ${data.customer}\n`;

  for (const performance of data.performances) {
    result += ` ${performance.play.name}: ${formatCurrency(
      performance.amount / statementConfig.currency.minorUnitsPerMajorUnit
    )} (${performance.audience} seats)\n`;
  }

  result += `Amount owed is ${formatCurrency(
    data.totalAmount / statementConfig.currency.minorUnitsPerMajorUnit
  )}\n`;
  result += `You earned ${data.totalVolumeCredits} credits\n`;
  return result;
}
