<?php

namespace Prelude;

use Closure;
use Exception;
use ReflectionFunction;
use ReflectionMethod;

// General

/**
 * @param $item
 * @return mixed
 */
function id($item) {
    return $item;
}

/**
 * @param $item
 * @return Closure
 */
function constant($item) {
    return function() use ($item) { return $item; };
}

/**
 * @return Closure
 */
function noop() {
    return function() {};
}

// Comparison

/**
 * @param $a
 * @param $b
 * @return bool
 */
function equals($a, $b) {
    return $a === $b;
}

/**
 * @param $item
 * @return bool
 */
function not($item) {
    return ! $item;
}

/**
 * @param $a
 * @param $b
 * @return bool
 */
function gt($a, $b) {
    return $a > $b;
}

/**
 * @param $a
 * @param $b
 * @return bool
 */
function lt($a, $b) {
    return $a < $b;
}

/**
 * @param $a
 * @param $b
 * @return bool
 */
function gte($a, $b) {
    return $a >= $b;
}

/**
 * @param $a
 * @param $b
 * @return bool
 */
function lte($a, $b) {
    return $a <= $b;
}

// Maths

/**
 * @param $a
 * @param $b
 * @return mixed
 */
function add($a, $b) {
    return $a + $b;
}

/**
 * @param $a
 * @param $b
 * @return mixed
 */
function subtract($a, $b) {
    return $a - $b;
}

/**
 * @param $a
 * @param $b
 * @return mixed
 */
function multiply($a, $b) {
    return $a * $b;
}

/**
 * @param $a
 * @param $b
 * @return float|int
 */
function divide($a, $b) {
    return $a / $b;
}

/**
 * @param $a
 * @param $b
 * @return int
 */
function modulus($a, $b) {
    return $a % $b;
}

// Array Access

/**
 * @param $index
 * @param $value
 * @param array|\ArrayAccess $array
 * @return mixed
 */
function insert($index, $value, $array) {
    return $array[$index] = $value;
}

/**
 * @param $index
 * @param array|\ArrayAccess $array
 * @return bool
 */
function exists($index, $array) {
    return isset($array[$index]);
}

/**
 * @param $index
 * @param array|\ArrayAccess $array
 * @return mixed
 */
function remove($index, $array) {
    unset($array[$index]);
    return $array;
}

/**
 * @param $index
 * @param array|\ArrayAccess $array
 * @return mixed
 */
function pick($index, $array) {
    return $array[$index];
}

// String

/**
 * @param string $chars
 * @return array
 */
function chars($chars) {
    return str_split($chars);
}

/**
 * @param array $chars
 * @return string
 */
function unchars($chars) {
    return implode('', $chars);
}

/**
 * @param string $words
 * @return array
 */
function words($words) {
    return explode(' ', $words);
}

/**
 * @param array $words
 * @return string
 */
function unwords($words) {
    return implode(' ', $words);
}

/**
 * @param string $lines
 * @return array
 */
function lines($lines) {
    return explode(PHP_EOL, $lines);
}

/**
 * @param array $lines
 * @return string
 */
function unlines($lines) {
    return implode(PHP_EOL, $lines);
}

// Functions

/**
 * @param callable $callable
 * @return Closure
 */
function flip(callable $callable) {
    return function(...$params) use ($callable) {
        return $callable(...array_reverse($params));
    };
}

/**
 * @param callable $callable
 * @return Closure
 */
function splat(callable $callable) {
    return function($params) use ($callable) {
        return $callable(...$params);
    };
}

/**
 * @param callable $callable
 * @return Closure
 */
function unsplat(callable $callable) {
    return function(...$params) use ($callable) {
        return $callable($params);
    };
}

/**
 * @param callable $callable1
 * @param callable $callable2
 * @return Closure
 */
function compose(callable $callable1, callable $callable2) {
    return function(...$params) use ($callable1, $callable2) {
        return $callable1($callable2(...$params));
    };
}

/**
 * Create a partial application function for the start of the parameter list
 * @example invoke(partial(1, 2, 3), $callable)
 * @param array $params
 * @return Closure
 */
function partial(...$params) {
    return function (callable $callable) use ($params) {
        return function (...$more) use($params, $callable) {
            return $callable(...array_merge($params, $more));
        };
    };
}

/**
 * Create a partial application function for the end of the parameter list
 * @example invoke(partialEnd(1, 2, 3), $callable)
 * @param array $params
 * @return Closure
 */
function partialEnd(...$params) {
    return function (callable $callable) use ($params) {
        return function (...$more) use($params, $callable) {
            return $callable(...array_merge($more, $params));
        };
    };
}

/**
 * Create a partial application function for an indexed argument
 * @example invoke(partialAt(1, 2), $callable)
 * @param $index
 * @param $param
 * @return Closure
 */
function partialAt($index, $param) {
    return function (callable $callable) use ($index, $param) {

    };
}

/**
 * Create a partial application function for a map of indexed arguments
 * @example invoke(partialAt([1 => 2, 3 => 4]), $callable)
 * @param $map
 * @return Closure
 */
function partialsAt($map) {
    return function (callable $callable) use ($map) {

    };
}

/**
 * Create a partial application function for a named argument
 * @example invoke(partialFor('foo', 3), $callable)
 * @param $name
 * @param $param
 * @return Closure
 */
function partialFor($name, $param) {
    return function (callable $callable) use ($name, $param) {

    };
}

/**
 * Create a partial application function for a map of named arguments
 * @example invoke(partialsFor(['bob' => 2, 'foo' => 4]), $callable)
 * @param $map
 * @return Closure
 */
function partialsFor($map) {
    return function (callable $callable) use ($map) {

    };
}

/**
 * @param callable $callable
 * @param array ...$params
 * @return mixed
 */
function invoke(callable $callable, ...$params) {
    return $callable(...$params);
}

/**
 * @param $method
 * @return Closure
 */
function method($method) {
    return function ($object) use($method) {
        return [$object, $method];
    };
}

/**
 * @param callable $callable
 * @return Closure
 */
function memoize(callable $callable) {
    return function(...$params) use ($callable) {
        static $cache = [];
        $key = md5(serialize($params));
        $cache[$key] = isset($cache[$key]) ? $cache[$key] : $callable(...$params);
        return $cache[$key];
    };
}

/**
 * @param callable $callable
 * @param null $default
 * @return Closure
 */
function once(callable $callable, $default = null) {
    return function(...$params) use ($callable, $default) {
        static $run = false;
        $result = $run ? $default : $callable(...$params) ;
        $run = true;
        return $result; 
    };
}

/**
 * @param $index
 * @param null $default
 * @return Closure
 */
function nthArg($index, $default = null) {
    return function(...$params) use ($index, $default) {
        return isset($params[$index]) ? $params[$index] : $default;
    };
}

/**
 * @param array ...$indices
 * @return Closure
 */
function nthArgs(...$indices) {
    return function(...$params) use ($indices) {
        return array_values(array_intersect_key($params, array_flip($indices)));
    };
}

/**
 * @param callable $callable
 * @return ReflectionFunction|ReflectionMethod
 * @throws Exception
 */
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

/**
 * @param callable $callable
 * @return int
 */
function getArity(callable $callable) {
    return reflectCallable($callable)->getNumberOfRequiredParameters();
}

/**
 * @param callable $callable
 * @param $arity
 * @return Closure
 */
function setArity(callable $callable, $arity) {
    return function (...$params) use ($callable, $arity) {
        return $callable(...array_slice($params, 0, $arity));
    };
}

/**
 * @param callable $callable
 * @param null $count
 * @return callable|Closure
 */
function curry(callable $callable, $count = null) {
    $count = is_null($count) ? getArity($callable) : $count;
    return $count === 0 ? $callable : function (...$params) use($callable, $count) {
        $partial = invoke(partial(...$params), $callable); /** @type $partial callable */
        return count($params) >= $count ? $partial() : curry($partial, $count - count($params));
    };
}
