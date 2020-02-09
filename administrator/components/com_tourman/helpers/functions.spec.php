<?php
require(realpath(__DIR__ . '/functions.php'));

$testCount = 0;
$failedCount = 0;

function println($message) {
    echo("$message\n");
}

function redText($text) {
    return "\033[31m$text\033[0m";
}

function greenText($text) {
    return "\033[32m$text\033[0m";
}

function test(string $message, callable $testRunnerFunction) {
    global $testCount, $failedCount;
    $comparerCalled = false;
    $comparerHadError = false;
    $comparerResult = null;
    $errorMessage = '';

    $comparator = function($received, $expected) use(&$comparerCalled, &$comparerResult, &$errorMessage) {
       if ($comparerResult === false) {
          return;
       }

       $comparerCalled = true;

       if ($expected === $received) {
          if ($comparerResult === null) {
              $comparerResult = true;
          }
       } else {
          $comparerResult = false;

          $errorMessage = '    ' .
              greenText($expected) .
              '(' . gettype($expected) . ')' .
              ' expected but ' .
              redText($received) .
              '(' . gettype($received) . ')' .
              ' received';
       }
    };

    try {
        $testRunnerFunction($comparator);
    } catch (Exception $e) {
        $comparerHadError = true;
    }

    $testCount = $testCount + 1;

    if ($comparerResult) {
        println($message . ' - ' . greenText('✔'));
        return;
    }

    if (!$comparerCalled) {
        println(redText($message . ' - ✘'));
        println('    Test should call expect func at least once!');
        $failedCount = $failedCount + 1;
    } elseif ($comparerHadError) {

    } else {
        println(redText($message . ' - ✘'));
        println($errorMessage);
    }
}

println('getWinnerAction');
println('2-0');
test('should proceed to 2nd round', function($expect) {
    $res = getWinnerAction(3, 16, '2-0', 'w', 0);
    $expect($res['targetGame']['phase'], 'w1');
});
test('should proceed to 2nd round with correct phase placement', function($expect) {
    $res = getWinnerAction(3, 16, '2-0', 'w', 0);
    $expect($res['targetGame']['phasePlacement'], 1);
});

println('2-1');
test('should proceed to 1st round of olympics', function($expect) {
    $res = getWinnerAction(0, 16, '2-1', 'w', 2);
    $expect($res['targetGame']['phase'], 'o0');
});
test('should proceed to 1st slot in game', function($expect) {
    $res = getWinnerAction(0, 16, '2-1', 'w', 2);
    $expect($res['targetGame']['position'], 1);
});

println('getLoserAction');
println('2-1');
test('should proceed to 2nd round', function($expect) {
    $res = getLoserAction(3, 16, '2-1', 'l', 2);
    $expect($res['targetGame']['phase'], 'w1');
});


println('    Total ' . $testCount . ' tests');

if ($failedCount > 0) {
    println('    ' . redText($failedCount) . ' failed');
    exit(1);
}
?>
