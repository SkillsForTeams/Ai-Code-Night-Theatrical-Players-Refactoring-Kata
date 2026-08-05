package theatricalplays;

public class TragedyCalculator extends PerformanceCalculator {

    private static final int BASE_AMOUNT_CENTS = 40000;
    private static final int FREE_ATTENDANCE_THRESHOLD = 30;
    private static final int EXTRA_CENTS_PER_ATTENDEE_OVER_THRESHOLD = 1000;

    public TragedyCalculator(Performance performance, Play play) {
        super(performance, play);
    }

    @Override
    public int amount() {
        var amount = BASE_AMOUNT_CENTS;
        if (performance.getAudience() > FREE_ATTENDANCE_THRESHOLD) {
            amount += EXTRA_CENTS_PER_ATTENDEE_OVER_THRESHOLD * (performance.getAudience() - FREE_ATTENDANCE_THRESHOLD);
        }
        return amount;
    }
}
