package theatricalplays;

public abstract class PerformanceCalculator {

    private static final int FREE_VOLUME_CREDIT_THRESHOLD = 30;

    protected final Performance performance;
    protected final Play play;

    protected PerformanceCalculator(Performance performance, Play play) {
        this.performance = performance;
        this.play = play;
    }

    public static PerformanceCalculator create(Performance performance, Play play) {
        return switch (play.getType()) {
            case "tragedy" -> new TragedyCalculator(performance, play);
            case "comedy" -> new ComedyCalculator(performance, play);
            default -> throw new Error("unknown type: %s".formatted(play.getType()));
        };
    }

    /** Amount owed for this performance, in cents. */
    public abstract int amount();

    public int volumeCredits() {
        return Math.max(performance.getAudience() - FREE_VOLUME_CREDIT_THRESHOLD, 0);
    }
}
