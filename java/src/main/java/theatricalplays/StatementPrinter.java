package theatricalplays;

import java.text.NumberFormat;
import java.util.Locale;
import java.util.Map;

public class StatementPrinter {

    private static final int CENTS_PER_DOLLAR = 100;

    public String print(Invoice invoice, Map<String, Play> plays) {
        var totalAmount = 0;
        var volumeCredits = 0;
        var result = new StringBuilder(String.format("Statement for %s%n", invoice.getCustomer()));

        NumberFormat frmt = NumberFormat.getCurrencyInstance(Locale.US);

        for (var perf : invoice.getPerformances()) {
            var play = plays.get(perf.getPlayID());
            var calculator = PerformanceCalculator.create(perf, play);
            var thisAmount = calculator.amount();

            volumeCredits += calculator.volumeCredits();

            result.append(String.format("  %s: %s (%s seats)%n",
                    play.getName(), frmt.format(thisAmount / CENTS_PER_DOLLAR), perf.getAudience()));
            totalAmount += thisAmount;
        }
        result.append(String.format("Amount owed is %s%n", frmt.format(totalAmount / CENTS_PER_DOLLAR)));
        result.append(String.format("You earned %s credits%n", volumeCredits));
        return result.toString();
    }
}
