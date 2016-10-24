<?php

namespace Prelude;

use Closure;
use Exception;
use ReflectionFunction;
use ReflectionMethod;

// General

function id($item) {
    return $item;
}

function constant($item) {
    return function() use ($item) { return $item; };
}

function noop() {
    return constant(null);
}

// Comparison

function equals($a, $b) {
    return $a === $b;
}

function not($item) {
    return ! $item;
}

function gt($a, $b) {
    return $a > $b;
}

function lt($a, $b) {
    return $a < $b;
}

function gte($a, $b) {
    return $a >= $b;
}

function lte($a, $b) {
    return $a <= $b;
}

// Maths

function add($a, $b) {
    return $a + $b;
}

function subtract($item) {
    return $a - $b;
}

function multiply($a, $b) {
    return $a * $b;
}

function divide($a, $b) {
    return $a / $b;
}

function modulus($a, $b) {
    return $a % $b;
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

function partialEnd(callable $callable, ...$params) {
    return function (...$more) use($params, $callable) {
        return $callable(...array_merge($more, $params));
    };
}

function partialArgs(...$params) {
    return function (callable $callable) use ($params) {
        return partial($callable, ...$params);
    }
}

function partialEndArgs(...$params) {
    return function (callable $callable) use ($params) {
        return partialEnd($callable, ...$params);
    }
}

function apply(callable $callable, ...$params) {
    return $callable(...$params);
}

function applyArgs(...$params) {
    return function (callable $callable) use ($params) {
        return $callable(...$params);
    }
}

function method($method) {
    return function ($object) use($method) {
        return [$object, $method];
    };
}

function memoize(callable $callable) {
    return function(...$params) use ($callable) {
        static $cache = [];
        $key = md5(serialize($params));
        $cache[$key] = isset($cache[$key]) ? $cache[$key] : $callable(...$params);
        return $cache[$key];
    };
}

function once(callable $callable, $default = null) {
    return function(...$params) use ($callable, $default) {
        static $run = false;
        $result = $run ? $default : $callable(...$params) ;
        $run = true;
        return $result; 
    };
}

function nthArg($index, $default = null) {
    return function(...$params) use ($index, $default) {
        return isset($params[$index]) ? $params[$index] : $default;
    };
}

function nthArgs(...$indices) {
    return function(...$params) use ($indices) {
        return array_values(array_intersect_key($params, array_flip($indices)));
    };
}

function reflectCallable(callable $callable) {
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

function getArity(callable $callable) {
    return reflectCallable($callable)->getNumberOfRequiredParameters();
}

function setArity(callable $callable, $arity) {
    return function (...$params) use ($arity) {
        return $callable(...array_slice($params, 0, $arity));
    };
}

function curry(callable $callable, $count = null) {
    $count = is_null($count) ? getArity($callable) : $count;
    return $count === 0 ? $callable : function (...$params) use($callable, $count) {
        $apply = partial($callable, ...$params);
        return count($params) >= $count ? $apply() : curry($apply, $count - count($params));
    };
}
