import { createStatementData } from "./createStatementData";
import { Invoice, Plays } from "./domain";
import { renderText } from "./renderText";

export function statement(invoice: Invoice, plays: Plays): string {
  return renderText(createStatementData(invoice, plays));
}
