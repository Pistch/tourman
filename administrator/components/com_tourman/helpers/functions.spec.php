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
    $testCount = $testCount + 1;

    try {
        $testRunnerFunction($comparator);
    } catch (Exception $e) {
        $comparerHadError = true;
    }


    if ($comparerResult) {
        println($message . ' - ' . greenText('✔'));
        return;
    }

    $failedCount = $failedCount + 1;

    if (!$comparerCalled) {
        println(redText($message . ' - ✘'));
        println('    Test should call expect func at least once!');
    } elseif ($comparerHadError) {
        println(redText($message . ' - ✘'));
        println('    Test thrown an exception!');
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
    $expect($res['targetGame'], null);
    $expect($res['place'], 7);
});


println(' ');
println('Total ' . $testCount . ' tests');

if ($failedCount > 0) {
    println('    ' . redText($failedCount) . ' failed');
    exit(1);
}
?>
