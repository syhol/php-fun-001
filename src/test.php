<?php

namespace Prelude\Test;

use Exception;

function writeGreen($string) {
    echo "\033[32m$string\033[0m" . PHP_EOL;
}

function writeRed($string) {
    echo "\033[31m$string\033[0m" . PHP_EOL;
}

function handleError(array $error) {
    writeRed('--|');
    writeRed('  |> Error:');
    writeRed('  |> ' . $error['2'] . ':' . $error['3']);
    writeRed('  |> ' . $error['1']);
}

function handleException(Exception $exception) {
    writeRed('--|');
    writeRed('  |> Exception: ' . get_class($exception));
    writeRed('  |> ' . $exception->getFile() . ':' . $exception->getLine());
    writeRed('  |> ' . $exception->getMessage());
}

function handleFail($function, array $errors, Exception $exception = null) {
    writeRed(" \u{2718} " . $function);
    array_map('Prelude\Test\handleError', $errors);
    handleException($exception);
    return false;
}

function handleSuccess($function) {
    writeGreen(" \u{2714} " . $function);
    return true;
}

function errorHandler($errors = []) {
    set_error_handler(function (...$error) use (&$errors) {
        $errors[] = $error;
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
