<?php
namespace Prelude\Data;

use Prelude\Contract\Functor;

class Just extends Maybe
{

    public function apply(Functor $callable)
    {
        // TODO: Implement apply() method.
    }

    public function map(callable $callable)
    {
        // TODO: Implement map() method.
    }

    public function bind(callable $callable)
    {
        // TODO: Implement bind() method.
    }

    public function export()
    {
        // TODO: Implement export() method.
    }

    public static function pure($value)
    {
        // TODO: Implement pure() method.
    }
}