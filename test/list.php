<?php

use function Prelude\Collection\drop;
use function Prelude\Collection\iterate;
use function Prelude\Collection\take;
use function Prelude\Test\testFunction;

testFunction('Prelude\id', function (callable $id) {
    assert($id('foo') === 'foo');
    assert($id(123) === 123);
    assert($id([1, 2]) === [1, 2]);
    $obj = (object)['foo' => 'bar'];
    assert($id($obj) === $obj);
    assert($id((object)['foo' => 'bar']) !== (object)['foo' => 'bar']);
});

testFunction('Prelude\Collection\take', function (callable $take) {
    $array = iterator_to_array($take(5, drop(7, iterate(function ($a) { return $a + 1; }, 0))));
    assert($array === [8, 9, 10, 11, 12]);
});

testFunction('Prelude\Collection\reverse', function (callable $reverse) {
    assert($reverse([1, 2, 3]) === [3, 2, 1]);
});

testFunction('Prelude\Collection\sum', function (callable $sum) {
    assert($sum([1, 2, 3, 4]) === 10);
});

testFunction('Prelude\Collection\product', function (callable $product) {
    assert($product([1, 2, 8, 2]) === 32);
});
