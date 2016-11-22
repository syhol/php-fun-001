<?php

namespace Prelude\Collection;

use Exception;
use Generator;
use Prelude\Contract\Monoid;
use Prelude\Data\Nothing;
use function Prelude\invoke;
use Prelude\Contract\Applicative;
use Prelude\Contract\Functor;
use Prelude\Contract\Monad;
use Prelude\Data\Vector;
use Prelude\Data\Just;
use Prelude\Data\Map;
use Prelude\Data\Str;
use function Prelude\flip;
use function Prelude\partial;
use Traversable as BaseTraversable;

// Collections and Mapping

/**
 * @param $collection
 * @return Monoid
 * @throws Exception
 */
function getMonoid($collection) {
    if ($collection instanceof Monoid) return $collection;
    $monoid = getMonad($collection);
    if ($monoid instanceof Monoid) return $collection;
    throw new Exception('Can\'t find a monoid for value: ' . var_export($collection));
}
/**
 * @param $collection
 * @return Functor
 */
function getFunctor($collection) {
    return ($collection instanceof Functor) ? $collection : getMonad($collection);
}

/**
 * @param $collection
 * @return Applicative
 */
function getApplicative($collection) {
    return ($collection instanceof Applicative) ? $collection : getMonad($collection);
}

/**
 * @param $collection
 * @return Monad
 */
function getMonad($collection) {
    return $collection instanceof Monad ? $collection
        : is_array($collection) && isAssociative($collection) ? new Vector($collection)
        : is_array($collection) && (!isAssociative($collection)) ? new Map($collection)
        : $collection instanceof BaseTraversable ? new Vector(iterator_to_array($collection))
        : is_string($collection) ? new Str($collection)
        : is_null($collection) ? new Nothing
        : new Just($collection);
}

/**
 * @param array $array
 * @return bool
 */
function isAssociative(array $array) {
    return array_keys($array) !== range(0, count($array) - 1);
}

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
 * @param array $array
 * @return array
 */
function uncons(array $array) {
    $item = array_pop($array);
    return [$item, $array];
}

/**
 * @param $array
 * @param $more
 * @return Monoid
 */
function append(array $array, array ...$more) {
    return foldl(function (Monoid $monoid, $array) {
        return $monoid->append(getMonoid($array));
    }, getMonoid($array), $more);
}

/**
 * @param array $collections
 * @return array
 */
function concat($collections) {
    return getMonoid($collections)->concat();
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
 * @param $collection
 * @return mixed
 */
function map(callable $callable, $collection) {
    return $collection instanceof Functor
        ? $collection->map($callable)
        : getFunctor($collection)->map($callable)->export();
}

/**
 * @param $ap1
 * @param $ap2
 * @return mixed
 */
function apply($ap1, $ap2) {
    $ap2 = $ap2 instanceof Applicative ? $ap2 : getApplicative($ap2);
    return $ap1 instanceof Applicative
        ? $ap1->apply($ap2)
        : getApplicative($ap1)->apply($ap2)->export();
}

/**
 * @param $collection
 * @param callable $callable
 * @return mixed
 */
function bind($collection, callable $callable) {
    return $collection instanceof Monad
        ? $collection->bind($callable)
        : getMonad($collection)->bind($callable)->export();
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
 * @return mixed
 */
function tail($array) {
    return drop(1, $array);
}

/**
 * @param $array
 * @return mixed
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
 * @param $array
 * @return integer
 */
function sum($array) {
    return foldl('Prelude\add', 0, $array);
}

/**
 * @param $array
 * @return integer
 */
function product($array) {
    return foldl('Prelude\multiply', 1, $array);
}

/**
 * @param $array
 * @return array
 */
function nub($array) {
    return array_unique($array);
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
function elem($item, $iterator) {
    return any(invoke(partial($item), 'Prelude\equals'), $iterator);
}

/**
 * @param $item
 * @param $iterator
 * @return mixed
 */
function pluck($item, $iterator) {
    return map(invoke(partial($item), 'Prelude\pick'), $iterator);
}

/**
 * @param $iterator
 * @return mixed
 */
function keys($iterator) {
    return array_keys($iterator);
}

/**
 * @param $iterator
 * @return mixed
 */
function values($iterator) {
    return array_values($iterator);
}

/**
 * @param $size
 * @param $collection
 * @return mixed
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
    return array_slice($collection, 0, $size);
}

/**
 * @param $size
 * @param $collection
 * @return mixed
 */
function takeEnd($size, $collection) {
    return array_slice($collection, 0 - $size);
}

/**
 * @param callable $predicate
 * @param $collection
 * @return mixed
 */
function takeWhile(callable $predicate, $collection) {
    $array = [];
    foreach ($collection as $item) {
        if (!$predicate($item)) break;
        $array[] = $item;
    }
    return $array;
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
    return array_slice($collection, $size);
}

/**
 * @param $size
 * @param $collection
 * @return mixed
 */
function dropEnd($size, $collection) {
    return array_slice($collection, 0, 0 - $size);
}

/**
 * @param callable $predicate
 * @param $collection
 * @return mixed
 */
function dropWhile(callable $predicate, $collection) {
    foreach ($collection as $key => $item) {
        if (!$predicate($item)) break;
        unset($collection[$key]);
    }
    return array_values($collection);
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
    $matrix = map(invoke(partial(min(map('count', $arrays))), 'Prelude\Collection\take'), $arrays);
    while(!all('Prelude\not', $matrix)) {
        $zipped = append($zipped, map('Prelude\Collection\head', $matrix));
        $matrix = map('Prelude\Collection\tail', $matrix);
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
function pairs(array $array) {
    return zip(array_keys($array), $array);
}

/**
 * @param array $array
 * @return array
 */
function unpairs(array $array) {
    return array_combine(map('Prelude\Collection\head', $array), map('Prelude\Collection\last', $array));
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
