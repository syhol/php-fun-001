<?php
namespace Prelude\Data;

use Prelude\Contract\Functor;
use Prelude\Contract\Monad;
use Prelude\Contract\Monoid;
use Prelude\Contract\Show;
use Prelude\Contract\Read;

class Set implements Monad, Monoid, Show, Read
{

    public function apply(Functor $callable)
    {
        // TODO: Implement apply() method.
    }

    public function map(callable $callable)
    {
        // TODO: Implement map() method.
    }

    public function export()
    {
        // TODO: Implement export() method.
    }

    public function bind(callable $callable)
    {
        // TODO: Implement bind() method.
    }

    public function emptyValue()
    {
        // TODO: Implement emptyValue() method.
    }

    public function append($value)
    {
        // TODO: Implement append() method.
    }

    public function concat()
    {
        // TODO: Implement concat() method.
    }

    public function read($string)
    {
        // TODO: Implement read() method.
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
    }

    public static function pure($value)
    {
        // TODO: Implement pure() method.
    }
}