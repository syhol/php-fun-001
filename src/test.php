<?php

namespace Prelude\Test;

/**
 * @param callable $function
 * @param callable $test
 */
function testFunction(callable $function, callable $test) {
    try {
        error_clear_last();
        $test($function);
        $error = error_get_last();
        if ($error) {
            echo "Error" . PHP_EOL;
            print_r($error);
            return;
        }
    } catch (\Exception $exception) {
        echo "Error" . PHP_EOL;
        echo $exception;
        return;
    }

    echo $function . " tests passed" . PHP_EOL;
}