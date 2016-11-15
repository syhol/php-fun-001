<?php

namespace Prelude\Test;

use Exception;

function green($string) {
    return "\033[32m$string\033[0m";
}

function red($string) {
    return "\033[31m$string\033[0m";
}

function yellow($string) {
    return "\033[0;33m$string\033[0m";
}

function boldYellow($string) {
    return "\033[1;33m$string\033[0m";
}

function boldRed($string) {
    return "\033[1;31m$string\033[0m";
}

function cyan($string) {
    return "\033[0;36m$string\033[0m";
}

function writeIntro($title, $version) {
    $heading = "$title - v$version";
    echo boldYellow(" $heading ") . cyan(str_repeat('#', 32 - strlen($heading))) . PHP_EOL;
}

function writeSumary($title, $time, $passed, $failed) {
    echo cyan(" " . str_repeat('#', 32 - strlen($title))) . PHP_EOL .
         cyan("     |--> ") . boldYellow($title) . PHP_EOL .
         cyan("     |--> ") . yellow("Time: " . round($time * 1000, 3) . "ms") . PHP_EOL .
         cyan("     |--> ") . yellow("Tests: " . ($passed + $failed)) . PHP_EOL .
         cyan("     |--> ") . yellow("Passed: " . $passed) . PHP_EOL .
         cyan("     |--> ") . yellow("Failed: " . $failed) . PHP_EOL;
}

function parseErrorCode($error) {
    switch($error){
        case E_ERROR:               return 'Error';                  break;
        case E_WARNING:             return 'Warning';                break;
        case E_PARSE:               return 'Parse Error';            break;
        case E_NOTICE:              return 'Notice';                 break;
        case E_CORE_ERROR:          return 'Core Error';             break;
        case E_CORE_WARNING:        return 'Core Warning';           break;
        case E_COMPILE_ERROR:       return 'Compile Error';          break;
        case E_COMPILE_WARNING:     return 'Compile Warning';        break;
        case E_USER_ERROR:          return 'User Error';             break;
        case E_USER_WARNING:        return 'User Warning';           break;
        case E_USER_NOTICE:         return 'User Notice';            break;
        case E_STRICT:              return 'Strict Notice';          break;
        case E_RECOVERABLE_ERROR:   return 'Recoverable Error';      break;
        default:                    return "Unknown error ($error)"; break;
    }
}

function handleError(array $error) {
    echo red('  |') . PHP_EOL .
         red("  |> \e[1;31mError - " . parseErrorCode($error['0'])) . PHP_EOL .
         red('  |> File    : ' . $error['2'] . ':' . $error['3']) . PHP_EOL .
         red('  |> Message : ' . $error['1']) . PHP_EOL .
         red('  |> Trace   :') . PHP_EOL .
         red('  |>   ' . implode(PHP_EOL . '  |>   ', explode(PHP_EOL, trim($error['trace'])))) . PHP_EOL;
}

function handleException(Exception $exception) {
    echo red('  |') . PHP_EOL .
         red("  |> \e[1;31mException - " . get_class($exception)) . PHP_EOL .
         red('  |> File    : ' . $exception->getFile() . ':' . $exception->getLine()) . PHP_EOL .
         red('  |> Message : ' . $exception->getMessage()) . PHP_EOL .
         red('  |> Trace   :') . PHP_EOL .
         red('  |>   ' . implode(PHP_EOL . '  |>   ', explode(PHP_EOL, trim($exception->getTraceAsString())))) . PHP_EOL;
}

function handleFail($function, array $errors, Exception $exception = null) {
    echo red(" \u{2718} " . $function) . PHP_EOL;
    array_map('Prelude\Test\handleError', $errors);
    if ($exception) handleException($exception);
    echo red('  |') . PHP_EOL;
    global $failed;
    $failed++;
    return false;
}

function handleSuccess($function) {
    global $passed;
    $passed++;
    echo green(" \u{2714} " . $function) . PHP_EOL;
    return true;
}

function errorHandler($errors = []) {
    set_error_handler(function (...$error) use (&$errors) {
        ob_start();
        debug_print_backtrace();
        $errors[] = $error + ['trace' => ob_get_clean()];
    });

    return function () use (&$errors) {
        restore_error_handler();
        return $errors;
    };
}

/**
 * @param callable $function
 * @param callable $test
 * @return bool
 */
function testFunction(callable $function, callable $test) {
    $errorHandler = errorHandler();

    try {
        $test($function);
    } catch (Exception $exception) {
        return handleFail($function, $errorHandler(), $exception);
    }

    $errors = $errorHandler();
    return empty($errors) ? handleSuccess($function) : handleFail($function, $errors) ;
}
