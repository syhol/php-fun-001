<?php

namespace Prelude\Collection;

use Exception;
use Generator;
use function Prelude\apply;
use Prelude\Data\Just;
use Prelude\Data\Nothing;
use function Prelude\partial;

// Collections and Mapping

/**
 * @param string|array|\Countable $item
 * @return int
 */
function length($item) {
    return is_string($item) ? mb_strlen($item) : count($item);
}

/**
 * @param $item
 * @param array $array
 * @return mixed
 */
function cons($item, array $array) {
    $array[] = $item;
    return $array;
}

/**
 * @param array $array1
 * @param array $array2
 * @return array
 */
function append(array $array1, array $array2) {
    return concat([$array1, $array2]);
}

/**
 * @param array $collections
 * @return array
 */
function concat(array $collections) {
    return array_merge(...ary(map('Prelude\Collection\ary', $collections)));
}

/**
 * @param callable $callable
 * @param $initial
 * @param array $array
 * @return mixed
 */
function foldr(callable $callable, $initial, array $array) {
    foreach ($array as $item) {
        $initial = $callable($item, $initial);
    }
    return $initial;
}

/**
 * @param callable $callable
 * @param $initial
 * @param array $array
 * @return mixed
 */
function foldl(callable $callable, $initial, array $array) {
    return foldr(flip($callable), $initial, $array);
}

/**
 * @param callable $callable
 * @param array $array
 * @return array
 */
function map(callable $callable, array $array) {
    foreach ($array as $key => $value) {
        $array[$key] = $callable($value);
    }
    return $array;
}

/**
 * @param callable $callable
 * @param array $array
 * @return array
 */
function filter(callable $callable, array $array) {
    foreach ($array as $key => $value) {
        if(!$callable($value)) unset($array[$key]);
    }
    return $array;
}

/**
 * @param $array
 * @return mixed|null
 */
function head($array) {
    $head = take(1, $array);
    return count($head) > 0 ? array_pop($head) : null;
}

/**
 * @param $array
 * @return mixed|null
 */
function last($array) {
    $last = take(-1, $array);
    return count($last) > 0 ? array_pop($last) : null;
}

/**
 * @param $array
 * @return Generator
 */
function tail($array) {
    return drop(1, $array);
}

/**
 * @param $array
 * @return Generator
 */
function init($array) {
    return drop(-1, $array);
}

/**
 * @param $size
 * @param $array
 * @return array
 */
function chunk($size, $array) {
    return array_chunk($size, $array);
}

/**
 * @param $array
 * @return array
 */
function reverse($array) {
    return array_reverse($array);
}

/**
 * @param callable $callable
 * @param $array
 * @return bool
 */
function any(callable $callable, $array) {
    return count(filter($callable, $array)) > 0;
}

/**
 * @param callable $callable
 * @param $array
 * @return bool
 */
function all(callable $callable, $array) {
    return count(filter($callable, $array)) === count($array);
}

/**
 * @param $item
 * @param $iterator
 * @return bool
 */
function contains($item, $iterator) {
    return any(apply(partial('Prelude\\equals'), $item), $iterator);
}


/**
 * @param $item
 * @param $iterator
 * @return mixed
 */
function pluck($item, $iterator) {
    return map(apply(partial('Prelude\\pick'), $item), $iterator);
}

/**
 * @param $size
 * @param $collection
 * @return Generator
 */
function take($size, $collection) {
    if ($collection instanceof Generator) return takeGenerator($size, $collection);
    if (is_callable($size)) return takeWhile($size, $collection);
    return $size >= 0 ? takeStart($size, $collection) : takeEnd(abs($size), $collection);
}

/**
 * @param $size
 * @param $collection
 * @return mixed
 */
function drop($size, $collection) {
    if ($collection instanceof Generator) return dropGenerator($size, $collection);
    if (is_callable($size)) return dropWhile($size, $collection);
    return $size >= 0 ? dropStart($size, $collection) : dropEnd(abs($size), $collection);
}

/**
 * @param $size
 * @param $collection
 * @return mixed
 */
function takeStart($size, $collection) {
    return col($collection, array_slice(ary($collection), 0, $size));
}

/**
 * @param $size
 * @param $collection
 * @return mixed
 */
function takeEnd($size, $collection) {
    return col($collection, array_slice(ary($collection), 0 - $size));
}

/**
 * @param callable $predicate
 * @param $collection
 * @return mixed
 */
function takeWhile(callable $predicate, $collection) {
    $array = [];
    foreach (ary($collection) as $item) {
        if (!$predicate($item)) break;
        $array[] = $item;
    }
    return col($collection, $array);
}

/**
 * @param $size
 * @param Generator $collection
 * @return Generator
 * @throws Exception
 */
function takeGenerator($size, Generator $collection) {
    if ($size < 0) throw new Exception('Can\'t take items from end of Generators');
    $count = 0;
    while ($count++ < $size && $collection->valid()) {
        yield $collection->current();
        $collection->next();
    }
}

/**
 * @param $size
 * @param $collection
 * @return mixed
 */
function dropStart($size, $collection) {
    return col($collection, array_slice(ary($collection), $size));
}

/**
 * @param $size
 * @param $collection
 * @return mixed
 */
function dropEnd($size, $collection) {
    return col($collection, array_slice(ary($collection), 0, 0 - $size));
}

/**
 * @param callable $predicate
 * @param $collection
 * @return mixed
 */
function dropWhile(callable $predicate, $collection) {
    $array = ary($collection);
    foreach ($array as $key => $item) {
        if (!$predicate($item)) break;
        unset($array[$key]);
    }
    return col($collection, array_values($array));
}

/**
 * @param $size
 * @param Generator $collection
 * @return Generator
 * @throws Exception
 */
function dropGenerator($size, Generator $collection) {
    if ($size < 0) throw new Exception('Can\'t drop items from end of Generators');
    $count = 0;
    while ($count++ < $size && $collection->valid()) {
        $collection->next();
    }

    while ($collection->valid()) {
        yield $collection->current();
        $collection->next();
    }
}

/**
 * @param \array[] $arrays
 * @return array
 */
function zip(array ...$arrays) {
    $zipped = [];
    $matrix = map(apply(partial(min(map('count', $arrays))), 'Prelude\take'), $arrays);
    while(!all('Prelude\not', $matrix)) {
        $zipped[] = concat(map('Prelude\head', $matrix));
        $matrix = map('Prelude\tail', $matrix);
    }
    return $zipped;
}

/**
 * @param array $array
 * @return mixed
 */
function unzip(array $array) {
    return foldl('array_merge', $array, []);
}

/**
 * @param array $array
 * @return array
 */
function toPairs(array $array) {
    return zip(array_keys($array), $array);
}

/**
 * @param array $array
 * @return array
 */
function fromPairs(array $array) {
    return array_combine(concat(map('Prelude\head', $array)), concat(map('Prelude\last', $array)));
}

// Lists Generators

/**
 * @param callable $callable
 * @param $size
 * @return Generator
 */
function times(callable $callable, $size) {
    $count = 0;
    while ($count++ < $size) yield $callable();
}

/**
 * @param callable $callable
 * @param $initial
 * @return Generator
 */
function iterate(callable $callable, $initial) {
    while (true) yield $initial = $callable($initial);
}

/**
 * @param callable $predicate
 * @param callable $transform
 * @param $initial
 * @return Generator
 */
function until(callable $predicate, callable $transform, $initial) {
    while (!$predicate($initial)) yield $initial = $transform($initial);
}

/**
 * @param $item
 * @return Generator
 */
function repeat($item) {
    while (true) yield $item;
}

/**
 * @param $list
 * @return Generator
 */
function cycle($list) {
    while (true) foreach ($list as $item) yield $item;
}