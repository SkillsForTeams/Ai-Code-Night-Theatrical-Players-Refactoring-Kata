package theatricalplays;

public class ComedyCalculator extends PerformanceCalculator {

    private static final int BASE_AMOUNT_CENTS = 30000;
    private static final int BASE_AUDIENCE_THRESHOLD = 20;
    private static final int BONUS_CENTS_OVER_THRESHOLD = 10000;
    private static final int EXTRA_CENTS_PER_ATTENDEE_OVER_THRESHOLD = 500;
    private static final int CENTS_PER_ATTENDEE = 300;
    private static final int ATTENDEES_PER_BONUS_VOLUME_CREDIT = 5;

    public ComedyCalculator(Performance performance, Play play) {
        super(performance, play);
    }

    @Override
    public int amount() {
        var amount = BASE_AMOUNT_CENTS;
        if (performance.getAudience() > BASE_AUDIENCE_THRESHOLD) {
            amount += BONUS_CENTS_OVER_THRESHOLD
                    + EXTRA_CENTS_PER_ATTENDEE_OVER_THRESHOLD * (performance.getAudience() - BASE_AUDIENCE_THRESHOLD);
        }
        amount += CENTS_PER_ATTENDEE * performance.getAudience();
        return amount;
    }

    @Override
    public int volumeCredits() {
        return super.volumeCredits() + performance.getAudience() / ATTENDEES_PER_BONUS_VOLUME_CREDIT;
    }
}
