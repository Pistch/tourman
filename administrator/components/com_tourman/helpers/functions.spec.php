<?php
require('./functions.php');

$hasFailedTests = false;

function println($message) {
    echo("$message\n");
}

function redText($text) {
    return "\033[31m$text\033[0m";
}

function greenText($text) {
    return "\033[32m$text\033[0m";
}

function test($message, $received, $expected) {
    if ($received === $expected) {
        println($message . ' - ' . greenText('✔'));
        return;
    }

    $hasFailedTests = true;
    println(redText($message . ' - ✘'));
    println($expected . ' expected instead of ' . $received);
}

println('getWinnerAction');
println('2-0');
$res = getWinnerAction(3, 16, '2-0', 'w', 0);
test(
    'should proceed to 2nd round',
    $res['targetGame']['phase'],
    'w1'
);
test(
    'should proceed to 2nd round with correct phase placement',
    $res['targetGame']['phasePlacement'],
    1
);

if ($hasFailedTests) {
    exit(1);
}
?>