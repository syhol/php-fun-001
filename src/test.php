<?php

namespace Prelude\Test;

use Exception;

function writeGreen($string, $printEol = true) {
    echo "\033[32m$string\033[0m" . ($printEol ? PHP_EOL : '');
}

function writeRed($string, $printEol = true) {
    echo "\033[31m$string\033[0m" . ($printEol ? PHP_EOL : '');
}

function writeYellow($string, $printEol = true) {
    echo "\033[0;33m$string\033[0m" . ($printEol ? PHP_EOL : '');
}

function writeBoldYellow($string, $printEol = true) {
    echo "\033[1;33m$string\033[0m" . ($printEol ? PHP_EOL : '');
}

function writeBoldRed($string, $printEol = true) {
    echo "\033[1;31m$string\033[0m" . ($printEol ? PHP_EOL : '');
}

function writeCyan($string, $printEol = true) {
    echo "\033[0;36m$string\033[0m" . ($printEol ? PHP_EOL : '');
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
        default:                    return "Unknown error ($errno)"; break;
    }
}

function handleError(array $error) {
    writeRed('  |');
    writeRed("  |> Error   : \033[1;31m" . parseErrorCode($error['0']));
    writeRed('  |> File    : ' . $error['2'] . ':' . $error['3']);
    writeRed('  |> Message : ' . $error['1']);
    writeRed('  |> Trace   :');
    $trace = explode(PHP_EOL, trim($error['trace']));
    foreach ($trace as $item)
        writeRed('  |>   ' . $item);
}

function handleException(Exception $exception) {
    writeRed('  |');
    writeRed("  |> Exception : \033[1;31m" . get_class($exception));
    writeRed('  |> File      : ' . $exception->getFile() . ':' . $exception->getLine());
    writeRed('  |> Message   : ' . $exception->getMessage());
    writeRed('  |> Trace     :');
    $trace = explode(PHP_EOL, $exception->getTraceAsString());
    foreach ($trace as $item)
        writeRed('  |>   ' . $item);
}

function handleFail($function, array $errors, Exception $exception = null) {
    writeRed(" \u{2718} " . $function);
    array_map('Prelude\Test\handleError', $errors);
    if ($exception) handleException($exception);
    writeRed('  |');
    return false;
}

function handleSuccess($function) {
    writeGreen(" \u{2714} " . $function);
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
