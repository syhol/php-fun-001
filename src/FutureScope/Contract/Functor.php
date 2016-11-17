<?php
namespace Prelude\Contract;

interface Functor
{
    /**
     * @param callable $callable
     * @return static
     */
    public function map(callable $callable);
    public function pure($value);
    public function export();
}
