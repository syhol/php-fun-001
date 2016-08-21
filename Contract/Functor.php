<?php
namespace Prelude\Contract;

interface Functor
{
    public function map(callable $callable);
    public function pure($value);
}
