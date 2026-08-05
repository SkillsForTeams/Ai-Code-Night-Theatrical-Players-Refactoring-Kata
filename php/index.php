<?php

declare(strict_types=1);

use Theatrical\Invoice;
use Theatrical\Performance;
use Theatrical\Play;
use Theatrical\StatementPrinter;

require __DIR__ . '/vendor/autoload.php';

$plays = [
    'hamlet' => new Play('Hamlet', 'tragedy'),
    'as-like' => new Play('As You Like It', 'comedy'),
    'othello' => new Play('Othello', 'tragedy'),
];

$performances = [
    new Performance('hamlet', 55),
    new Performance('as-like', 35),
    new Performance('othello', 40),
];
$invoice = new Invoice('BigCo', $performances);

$statementPrinter = new StatementPrinter();

echo '<h1>Statement</h1>';
echo '<pre>' . htmlspecialchars($statementPrinter->print($invoice, $plays)) . '</pre>';

// TODO: swap StatementPrinter for an HtmlStatement class once it's written (Ch.1 page 31),
// for a richer HTML rendering of the same data.
