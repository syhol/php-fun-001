<?php

use function Prelude\Collection\drop;
use function Prelude\Collection\iterate;
use function Prelude\Collection\take;
use function Prelude\Collection\reverse;
use function Prelude\Collection\sum;
use function Prelude\Collection\product;
use function Prelude\id;

describe('Prelude\id', function () {
    it('should work with a string', function() {
        expect(id('foo'))->toBe('foo');
    });
    it('should work with an int', function() {
        expect(id(123))->toBe(123);
    });
    it('should work with an array', function() {
        expect(id([1, 2]))->toBe([1, 2]);
    });
    it('should work with an object', function() {
        $obj = (object)['foo' => 'bar'];
        expect(id($obj))->toBe($obj);
        expect(id((object)['foo' => 'bar']))->not()->toBe((object)['foo' => 'bar']);
    });
});

describe('Prelude\Collection\take', function () {
    it('should work with a string', function() {
        $array = iterator_to_array(take(5, drop(7, iterate(function ($a) { return $a + 1; }, 0))));
        expect($array)->toBe([8, 9, 10, 11, 12]);
    });
});

describe('Prelude\Collection\reverse', function () {
    it('should work with a string', function() {
        expect(reverse([1, 2, 3]))->toBe([3, 2, 1]);
    });
});

describe('Prelude\Collection\sum', function () {
    it('should generate the sum from an array of intergers', function() {
        expect(sum([1, 2, 3, 4]))->toBe(10);
    });
    it('should generate the sum from an array of floats', function() {
        expect(sum([0.1, 0.2, 0.3]))->toBeWithin(0.59999, 0.60001);
    });
});

describe('Prelude\Collection\product', function () {
    it('should generate the product from an array of intergers', function() {
        expect(product([1, 2, 8, 2]))->toBe(32);
    });
    it('should generate the product from an array of floats', function() {
        expect(product([0.1, 0.2, 0.3]))->toBeWithin(0.0059999, 0.0060001);
    });
});
