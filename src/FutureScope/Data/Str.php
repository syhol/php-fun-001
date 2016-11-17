<?php
namespace Prelude\Data;

use Prelude\Contract\Functor;
use Prelude\Contract\Monad;

class Str implements Monad
{

    public function apply(Functor $callable)
    {
        // TODO: Implement apply() method.
    }

    public function map(callable $callable)
    {
        // TODO: Implement map() method.
    }

    public function pure($value)
    {
        // TODO: Implement pure() method.
    }

    public function bind(callable $callable)
    {
        // TODO: Implement bind() method.
    }

    public function export()
    {
        // TODO: Implement export() method.
    }
}