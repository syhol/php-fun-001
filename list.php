<?php

namespace Prelude\Collection;

use Exception;
use Generator;
use Prelude\Data\Just;
use Prelude\Data\Nothing;

// Collections and Mapping

function ary($collection) {
    if (is_array($collection)) {
        return $collection;
    } elseif (is_string($collection)) {
        return str_split($collection);
    } elseif ($collection instanceof Arrayable) {
        return $collection->toArray();
    } elseif ($collection instanceof \Generator) {
        $array = [];
        while ($collection->valid()) {
            $array[] = $collection->current();
            $collection->next();
        }
        return $array;
    } elseif ($collection instanceof \Iterator) {
        return iterator_to_array($collection);
    }

    throw new Exception('Could not convert to array');
}

function col($collection, array $array) {
    if (is_string($collection)) {
        return implode('', $array);
    } elseif ($collection instanceof Arrayable) {
        return $collection->fromArray($array);
    }

    return $array;
}

function append($item, $collection) {
    $array = ary($collection);
    array_push($array, $item);
    return col($collection, $array);
}

function prepend($item, $collection) {
    $array = ary($collection);
    array_unshift($array, $item);
    return col($collection, $array);
}

function foldr(callable $callable, $initial, $collection) {
    foreach (ary($collection) as $item) {
        $initial = $callable($item, $initial);
    }
    return $initial;
}

function foldl(callable $callable, $initial, $array) {
    return foldr(flip($callable), $initial, $array);
}

function map(callable $callable, $collection) {
    $array = ary($collection);
    foreach ($array as $key => $value) {
        $array[$key] = $callable($value);
    }
    return col($collection, $array);
}

function filter(callable $callable, $collection) {
    $array = [];
    foreach ($collection as $key => $value) {
        if($callable($value)) $array[$key] = $value;
    }
    return col($collection, $array);
}

function head($array) {
    $head = take(1, $array);
    return count($head) > 0 ? new Just($head) : new Nothing;
}

function last($array) {
    $last = take(-1, $array);
}

function tail($array) {
    return drop(1, $array);
}

function init($array) {
    return drop(-1, $array);
}

function reverse($array) {
    return foldr('Prelude\prepend', [], $array);
}

function concat($collection) {
    return col($collection, array_merge(...ary($collection)));
}

function any(callable $callable, $array) {
    return count(filter($callable, $array)) > 0;
}

function all(callable $callable, $array) {
    return count(filter($callable, $array)) === count($array);
}

function contains($item, $iterator) {
    return any(partial('Prelude\equals', $item), $iterator);
}

function take($size, $collection) {
    if ($collection instanceof Generator) {
        return takeGenerator($size, $collection);
    }

    return $size > 0
        ? col($collection, array_slice(ary($collection), 0, $size))
        : col($collection, array_slice(ary($collection), $size));
}


function drop($size, $collection) {
    if ($collection instanceof Generator) {
        return dropGenerator($size, $collection);
    }

    return $size > 0
        ? col($collection, array_slice(ary($collection), $size))
        : col($collection, array_slice(ary($collection), 0, $size));
}

function takeGenerator($size, Generator $collection) {
    if ($size < 0) throw new Exception('Can\'t take items from end of Generators');
    $count = 0;
    while ($count++ < $size && $collection->valid()) {
        yield $collection->current();
        $collection->next();
    }
}

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

function zip(...$arrays) {
    $zipped = [];
    $matrix = map(partial('Prelude\take', min(map('count', $arrays))), $arrays);
    while(!all('Prelude\not', $matrix)) {
        $zipped[] = concat(map('Prelude\head', $matrix));
        $matrix = map('Prelude\tail', $matrix);
    }
    return $zipped;
}

function toPairs($array) {
    return zip(array_keys(ary($array)), ary($array));
}

function fromPairs($array) {
    return array_combine(concat(map('Prelude\head', $array)), concat(map('Prelude\last', $array)));
}

// Lists Generators

function times(callable $callable, $size) {
    $count = 0;
    while ($count++ < $size) yield $callable();
}

function iterate(callable $callable, $initial) {
    while (true) yield $initial = $callable($initial);
}

function repeat($item) {
    while (true) yield $item;
}

function cycle($list) {
    while (true) foreach ($list as $item) yield $item;
}

var_dump(iterator_to_array(take(5, drop(7, iterate(function ($a) { return $a + 1; }, 0)))));
