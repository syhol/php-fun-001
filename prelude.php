<?php

namespace Prelude;

use Closure;
use Exception;
use ReflectionFunction;
use ReflectionMethod;

// General

function equals($a, $b) {
    return $a === $b;
}

function null($item) {
    return ! $item;
}

function id($item) {
    return $item;
}

function constant($item) {
    return function() use ($item) { return $item; };
}

// Array Access

function insert($index, $value, $array) {
    return $array[$index] = $value;
}

function exists($index, $array) {
    return isset($array[$index]);
}

function remove($index, $array) {
    unset($array[$index]);
    return $array;
}

function pick($index, $array) {
    return $array[$index];
}

// Mapped Array Access

function pluck($index, $collection) {
    return map(partial('pick', $index), $collection);
}

// String

function chars($chars) {
    return str_split($chars);
}

function unchars($chars) {
    return implode('', $chars);
}

function words($words) {
    return explode(' ', $words);
}

function unwords($words) {
    return implode(' ', $words);
}

function lines($lines) {
    return explode(PHP_EOL, $lines);
}

function unlines($lines) {
    return implode(PHP_EOL, $lines);
}

// Lists Generators

function times(callable $callable, $size) {
    $count = 0;
    while ($count++ < $size) yield $callable();
}

function iterate(callable $callable, $initial) {
    while(true) yield $initial = $callable($initial);
}

function repeat($item) {
    while (true) yield $item;
}

function replicate($item, $size) {
    $count = 0;
    while ($count++ < $size) yield $item;
}

function cycle($list) {
    while (true) foreach ($list as $item) yield $item;
}

// Functions

function flip(callable $callable) {
    return function(...$params) use ($callable) {
        return $callable(...array_reverse($params));
    };
}

function splat(callable $callable) {
    return function($params) use ($callable) {
        return $callable(...$params);
    };
}

function unsplat(callable $callable) {
    return function(...$params) use ($callable) {
        return $callable($params);
    };
}

function compose(callable $callable1, callable $callable2) {
    return function(...$params) use ($callable1, $callable2) {
        return $callable1($callable2(...$params));
    };
}

function partial(callable $callable, ...$params) {
    return function (...$more) use($params, $callable) {
        return $callable(...array_merge($params, $more));
    };
}

function apply(callable $callable, ...$params) {
    return $callable(...$params);
}

function method($method) {
    return function ($object) use($method) {
        return [$object, $method];
    };
}

function reflect_callable(callable $callable) {
    $callable = (is_string($callable) && strpos($callable, '::') !== false)
        ? explode('::', $callable, 2)
        : $callable;

    if (is_array($callable) && count($callable) === 2) {
        list($class, $method) = array_values($callable);

        if (is_string($class) && ! method_exists($class, $method)) {
            $method = '__callStatic';
        }
        if (is_object($class) && ! method_exists($class, $method)) {
            $method = '__call';
        }
        return new ReflectionMethod($class, $method);
    } elseif ($callable instanceof Closure || is_string($callable)) {
        return new ReflectionFunction($callable);
    } elseif (is_object($callable) && method_exists($callable, '__invoke')) {
        return new ReflectionMethod($callable, '__invoke');
    }

    throw new Exception('Could not parse function');
}

function curry(callable $callable, $count = null) {
    $count = is_null($count) ? reflect_callable($callable)->getNumberOfRequiredParameters() : $count;
    return $count === 0 ? $callable : function (...$params) use($callable, $count) {
        $apply = partial($callable, ...$params);
        return count($params) >= $count ? $apply() : curry($apply, $count - count($params));
    };
}
