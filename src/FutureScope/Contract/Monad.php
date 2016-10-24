<?php
namespace Prelude\Contract;

interface Monad extends Applicative
{
    public function bind(callable $callable);
}
