<?php
namespace Prelude\Contract;

interface Monad extends Applicative
{
    /**
     * @param callable $callable
     * @return static
     */
    public function bind(callable $callable);
}
