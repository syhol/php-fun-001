<?php

use function Prelude\add;
use function Prelude\divide;
use function Prelude\equals;
use function Prelude\even;
use function Prelude\gt;
use function Prelude\gte;
use function Prelude\id;
use function Prelude\constant;
use function Prelude\lt;
use function Prelude\lte;
use function Prelude\modulus;
use function Prelude\multiply;
use function Prelude\noop;
use function Prelude\not;
use function Prelude\odd;
use function Prelude\subtract;

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
        $obj1 = (object)['foo' => 'bar'];
        $obj2 = (object)['foo' => 'bar'];
        expect(id($obj1))->toBe($obj1);
        expect(id($obj1))->not()->toBe($obj2);
    });
});

describe('Prelude\constant', function () {
    it('should always return the same value', function() {
        $obj1 = (object)['foo' => 'bar'];
        $obj2 = (object)['foo' => 'bar'];
        $constObj = constant($obj1);
        expect($constObj())->toBe($obj1);
        expect($constObj())->not()->toBe($obj2);
    });
    it('should always return the same integer, even when altered', function() {
        $int = 1;
        $constInt = constant($int);
        expect($constInt())->toBe(1);
        $int++;
        expect($int)->toBe(2);
        expect($constInt())->toBe(1);
    });
    it('should always return the same instance of a value', function() {
        $obj1 = (object)['foo' => 'bar'];
        $obj2 = (object)['foo' => 'bar'];
        $constObj = constant($obj1);
        expect($constObj())->toBe($obj1);
        expect($constObj()->foo)->toBe($obj2->foo);
        expect($constObj())->not()->toBe($obj2);
        $obj1->foo .= 'qux';
        expect($constObj())->toBe($obj1);
        expect($constObj()->foo)->not()->toBe($obj2->foo);
    });
});

describe('Prelude\noop', function () {
    it('should not have any output when invoked', function() {
        ob_start();
        $noop = noop();
        $noop();
        expect(ob_get_clean())->toBeEmpty();
    });
    it('should not return anying when invoked', function() {
        $noop = noop();
        expect($noop())->toBeNull();
    });
});

describe('Prelude\equals', function () {
    it('should check equality', function() {
        expect(equals(1, 1))->toBeTrue();
        expect(equals('a', 'a'))->toBeTrue();
        expect(equals([1, 'a'], [1, 'a']))->toBeTrue();
    });
    it('should check equality strictly', function() {
        expect(equals(1, 1.0))->toBeFalse();
        expect(equals(1, '1'))->toBeFalse();
        expect(equals(['1'], ['1', '2']))->toBeFalse();
        $object = (object)['foo' => 1];
        expect(equals($object, $object))->toBeTrue();
    });
});

describe('Prelude\not', function () {
    it('should make truthy into false', function() {
        expect(not(true))->toBeFalse();
        expect(not(1))->toBeFalse();
        expect(not(0.1))->toBeFalse();
        expect(not('yes'))->toBeFalse();
        expect(not((object)[]))->toBeFalse();
        expect(not([0]))->toBeFalse();
    });
    it('should make falsey into true', function() {
        expect(not(false))->toBeTrue();
        expect(not(0))->toBeTrue();
        expect(not(0.0))->toBeTrue();
        expect(not(''))->toBeTrue();
        expect(not('0'))->toBeTrue();
        expect(not(null))->toBeTrue();
        expect(not([]))->toBeTrue();
    });
});

describe('Prelude\gt', function () {
    it('should work with integers', function() {
        expect(gt(20, 10))->toBeTrue();
        expect(gt(10, 20))->toBeFalse();
    });
    it('should work with floats', function() {
        expect(gt(20.2, 20.1))->toBeTrue();
        expect(gt(20.1, 20.2))->toBeFalse();
    });
    it('should work huge integers', function() {
        expect(gt(PHP_INT_MAX, PHP_INT_MAX - 1))->toBeTrue();
        expect(gt(PHP_INT_MAX - 1, PHP_INT_MAX))->toBeFalse();
    });
    it('should work huge floats', function() {
        $bigFloat = (PHP_INT_MAX / 1000000) - 0.1;
        expect(gt($bigFloat - 0.1, $bigFloat - 0.9))->toBeTrue();
        expect(gt($bigFloat - 0.9, $bigFloat - 0.1))->toBeFalse();
    });
    it('should work tiny integers', function() {
        expect(gt(2, 1))->toBeTrue();
        expect(gt(1, 2))->toBeFalse();
    });
    it('should work tiny floats', function() {
        expect(gt(0.00000002, 0.00000001))->toBeTrue();
        expect(gt(0.00000001, 0.00000002))->toBeFalse();
    });
    it('should work negative integers', function() {
        expect(gt(-20, -10))->toBeFalse();
        expect(gt(-10, -20))->toBeTrue();
        expect(gt(10, -20))->toBeTrue();
        expect(gt(-20, 10))->toBeFalse();
    });
    it('should work negative floats', function() {
        expect(gt(-20.2, -20.1))->toBeFalse();
        expect(gt(-20.1, -20.2))->toBeTrue();
        expect(gt(20.1, -20.2))->toBeTrue();
        expect(gt(-20.2, 20.1))->toBeFalse();
    });
    it('should return false if value is the same', function() {
        expect(gt(10, 10))->toBeFalse();
        expect(gt(PHP_INT_MAX, PHP_INT_MAX))->toBeFalse();
        $bigFloat = (PHP_INT_MAX / 1000000) - 0.1;
        expect(gt($bigFloat, $bigFloat))->toBeFalse();
        expect(gt(0.00000001, 0.00000001))->toBeFalse();
        expect(gt(-20.2, -20.2))->toBeFalse();
    });
});

describe('Prelude\lt', function () {
    it('should work with integers', function() {
        expect(lt(20, 10))->toBeFalse();
        expect(lt(10, 20))->toBeTrue();
    });
    it('should work with floats', function() {
        expect(lt(20.2, 20.1))->toBeFalse();
        expect(lt(20.1, 20.2))->toBeTrue();
    });
    it('should work huge integers', function() {
        expect(lt(PHP_INT_MAX, PHP_INT_MAX - 1))->toBeFalse();
        expect(lt(PHP_INT_MAX - 1, PHP_INT_MAX))->toBeTrue();
    });
    it('should work huge floats', function() {
        $bigFloat = (PHP_INT_MAX / 1000000) - 0.1;
        expect(lt($bigFloat - 0.1, $bigFloat - 0.9))->toBeFalse();
        expect(lt($bigFloat - 0.9, $bigFloat - 0.1))->toBeTrue();
    });
    it('should work tiny integers', function() {
        expect(lt(2, 1))->toBeFalse();
        expect(lt(1, 2))->toBeTrue();
    });
    it('should work tiny floats', function() {
        expect(lt(0.00000002, 0.00000001))->toBeFalse();
        expect(lt(0.00000001, 0.00000002))->toBeTrue();
    });
    it('should work negative integers', function() {
        expect(lt(-20, -10))->toBeTrue();
        expect(lt(-10, -20))->toBeFalse();
        expect(lt(10, -20))->toBeFalse();
        expect(lt(-20, 10))->toBeTrue();
    });
    it('should work negative floats', function() {
        expect(lt(-20.2, -20.1))->toBeTrue();
        expect(lt(-20.1, -20.2))->toBeFalse();
        expect(lt(20.1, -20.2))->toBeFalse();
        expect(lt(-20.2, 20.1))->toBeTrue();
    });
    it('should return false if value is the same', function() {
        expect(lt(10, 10))->toBeFalse();
        expect(lt(PHP_INT_MAX, PHP_INT_MAX))->toBeFalse();
        $bigFloat = (PHP_INT_MAX / 1000000) - 0.1;
        expect(lt($bigFloat, $bigFloat))->toBeFalse();
        expect(lt(0.00000001, 0.00000001))->toBeFalse();
        expect(lt(-20.2, -20.2))->toBeFalse();
    });
});

describe('Prelude\gte', function () {
    it('should work with integers', function() {
        expect(gte(20, 10))->toBeTrue();
        expect(gte(10, 20))->toBeFalse();
    });
    it('should work with floats', function() {
        expect(gte(20.2, 20.1))->toBeTrue();
        expect(gte(20.1, 20.2))->toBeFalse();
    });
    it('should work huge integers', function() {
        expect(gte(PHP_INT_MAX, PHP_INT_MAX - 1))->toBeTrue();
        expect(gte(PHP_INT_MAX - 1, PHP_INT_MAX))->toBeFalse();
    });
    it('should work huge floats', function() {
        $bigFloat = (PHP_INT_MAX / 1000000) - 0.1;
        expect(gte($bigFloat - 0.1, $bigFloat - 0.9))->toBeTrue();
        expect(gte($bigFloat - 0.9, $bigFloat - 0.1))->toBeFalse();
    });
    it('should work tiny integers', function() {
        expect(gte(2, 1))->toBeTrue();
        expect(gte(1, 2))->toBeFalse();
    });
    it('should work tiny floats', function() {
        expect(gte(0.00000002, 0.00000001))->toBeTrue();
        expect(gte(0.00000001, 0.00000002))->toBeFalse();
    });
    it('should work negative integers', function() {
        expect(gte(-20, -10))->toBeFalse();
        expect(gte(-10, -20))->toBeTrue();
        expect(gte(10, -20))->toBeTrue();
        expect(gte(-20, 10))->toBeFalse();
    });
    it('should work negative floats', function() {
        expect(gte(-20.2, -20.1))->toBeFalse();
        expect(gte(-20.1, -20.2))->toBeTrue();
        expect(gte(20.1, -20.2))->toBeTrue();
        expect(gte(-20.2, 20.1))->toBeFalse();
    });
    it('should return true if value is the same', function() {
        expect(gte(10, 10))->toBeTrue();
        expect(gte(PHP_INT_MAX, PHP_INT_MAX))->toBeTrue();
        $bigFloat = (PHP_INT_MAX / 1000000) - 0.1;
        expect(gte($bigFloat, $bigFloat))->toBeTrue();
        expect(gte(0.00000001, 0.00000001))->toBeTrue();
        expect(gte(-20.2, -20.2))->toBeTrue();
    });
});

describe('Prelude\lte', function () {
    it('should work with integers', function() {
        expect(lte(20, 10))->toBeFalse();
        expect(lte(10, 20))->toBeTrue();
    });
    it('should work with floats', function() {
        expect(lte(20.2, 20.1))->toBeFalse();
        expect(lte(20.1, 20.2))->toBeTrue();
    });
    it('should work huge integers', function() {
        expect(lte(PHP_INT_MAX, PHP_INT_MAX - 1))->toBeFalse();
        expect(lte(PHP_INT_MAX - 1, PHP_INT_MAX))->toBeTrue();
    });
    it('should work huge floats', function() {
        $bigFloat = (PHP_INT_MAX / 1000000) - 0.1;
        expect(lte($bigFloat - 0.1, $bigFloat - 0.9))->toBeFalse();
        expect(lte($bigFloat - 0.9, $bigFloat - 0.1))->toBeTrue();
    });
    it('should work tiny integers', function() {
        expect(lte(2, 1))->toBeFalse();
        expect(lte(1, 2))->toBeTrue();
    });
    it('should work tiny floats', function() {
        expect(lte(0.00000002, 0.00000001))->toBeFalse();
        expect(lte(0.00000001, 0.00000002))->toBeTrue();
    });
    it('should work negative integers', function() {
        expect(lte(-20, -10))->toBeTrue();
        expect(lte(-10, -20))->toBeFalse();
        expect(lte(10, -20))->toBeFalse();
        expect(lte(-20, 10))->toBeTrue();
    });
    it('should work negative floats', function() {
        expect(lte(-20.2, -20.1))->toBeTrue();
        expect(lte(-20.1, -20.2))->toBeFalse();
        expect(lte(20.1, -20.2))->toBeFalse();
        expect(lte(-20.2, 20.1))->toBeTrue();
    });
    it('should return true if value is the same', function() {
        expect(lte(10, 10))->toBeTrue();
        expect(lte(PHP_INT_MAX, PHP_INT_MAX))->toBeTrue();
        $bigFloat = (PHP_INT_MAX / 1000000) - 0.1;
        expect(lte($bigFloat, $bigFloat))->toBeTrue();
        expect(lte(0.00000001, 0.00000001))->toBeTrue();
        expect(lte(-20.2, -20.2))->toBeTrue();
    });
});

describe('Prelude\add', function () {
    it('should add integers', function() {
        expect(add(20, 30))->toBe(50);
    });
    it('should add floats', function() {
        expect(add(20.2, 30.3))->toBe(50.5);
    });
    it('should add negative integers', function() {
        expect(add(-2000000, -3000000))->toBe(-5000000);
    });
    it('should add negative floats', function() {
        expect(add(-0.00002, -0.00003))->toBe(-0.00005);
    });
});

describe('Prelude\subtract', function () {
    it('should subtract integers', function() {
        expect(subtract(30, 20))->toBe(10);
    });
    it('should subtract floats', function() {
        expect(subtract(30.6, 20.1))->toBe(10.5);
    });
    it('should subtract negative integers', function() {
        expect(subtract(-300, -200))->toBe(-100);
    });
    it('should subtract negative floats', function() {
        expect(subtract(-0.003, -0.002))->toBe(-0.001);
    });
    it('should subtract a positive and a positive into a negative', function() {
        expect(subtract(20, 30))->toBe(-10);
    });
    it('should subtract a negative and a negative into a positive', function() {
        expect(subtract(-20, -30))->toBe(10);
    });
});

describe('Prelude\multiply', function () {
    it('should multiply integers', function() {
        expect(multiply(20, 3))->toBe(60);
    });
    it('should multiply floats', function() {
        expect(multiply(0.2, 4))->toBe(0.8);
    });
});

describe('Prelude\divide', function () {
    it('should divide integers', function() {
        expect(divide(20, 4))->toBe(5);
    });
    it('should divide floats', function() {
        expect(divide(0.2, 4))->toBe(0.05);
    });
});

describe('Prelude\modulus', function () {
    it('should modulus', function() {
        expect(modulus(4, 2))->toBe(0);
        expect(modulus(3, 2))->toBe(1);
    });
});

describe('Prelude\even', function () {
    it('should see even as even', function() {
        expect(even(2))->toBeTrue();
        expect(even(4))->toBeTrue();
        expect(even(6))->toBeTrue();
    });
    it('should not see odd as even', function() {
        expect(even(1))->toBeFalse();
        expect(even(3))->toBeFalse();
        expect(even(5))->toBeFalse();
    });
});

describe('Prelude\odd', function () {
    it('should see odd as odd', function() {
        expect(odd(1))->toBeTrue();
        expect(odd(3))->toBeTrue();
        expect(odd(5))->toBeTrue();
    });
    it('should not see even as odd', function() {
        expect(odd(2))->toBeFalse();
        expect(odd(4))->toBeFalse();
        expect(odd(6))->toBeFalse();
    });
});

describe('Prelude\insert', function () {
    it('should ...', function() {

    });
});

describe('Prelude\exists', function () {
    it('should ...', function() {

    });
});

describe('Prelude\remove', function () {
    it('should ...', function() {

    });
});

describe('Prelude\pick', function () {
    it('should ...', function() {

    });
});

describe('Prelude\chars', function () {
    it('should ...', function() {

    });
});


describe('Prelude\unchars', function () {
    it('should ...', function() {

    });
});

describe('Prelude\words', function () {
    it('should ...', function() {

    });
});

describe('Prelude\unwords', function () {
    it('should ...', function() {

    });
});

describe('Prelude\lines', function () {
    it('should ...', function() {

    });
});

describe('Prelude\unlines', function () {
    it('should ...', function() {

    });
});

describe('Prelude\flip', function () {
    it('should ...', function() {

    });
});

describe('Prelude\splat', function () {
    it('should ...', function() {

    });
});

describe('Prelude\unsplat', function () {
    it('should ...', function() {

    });
});

describe('Prelude\compose', function () {
    it('should ...', function() {

    });
});

describe('Prelude\invoke', function () {
    it('should ...', function() {

    });
});

describe('Prelude\method', function () {
    it('should ...', function() {

    });
});

describe('Prelude\memoize', function () {
    it('should ...', function() {

    });
});

describe('Prelude\once', function () {
    it('should ...', function() {

    });
});

describe('Prelude\nthArg', function () {
    it('should ...', function() {

    });
});

describe('Prelude\nthArgs', function () {
    it('should ...', function() {

    });
});

describe('Prelude\reflectCallable', function () {
    it('should ...', function() {

    });
});

describe('Prelude\getArity', function () {
    it('should ...', function() {

    });
});

describe('Prelude\setArity', function () {
    it('should ...', function() {

    });
});

describe('Prelude\curry', function () {
    it('should ...', function() {

    });
});

// Unstable

describe('Prelude\partial', function () {
    it('should ...', function() {

    });
});

describe('Prelude\partialEnd', function () {
    it('should ...', function() {

    });
});

describe('Prelude\partialAt', function () {
    it('should ...', function() {

    });
});

describe('Prelude\partialsAt', function () {
    it('should ...', function() {

    });
});

describe('Prelude\partialFor', function () {
    it('should ...', function() {

    });
});

describe('Prelude\partialsFor', function () {
    it('should ...', function() {

    });
});
