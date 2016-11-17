<?php
namespace Prelude\Contract;

interface Applicative extends Functor
{
    /**
     * @param Functor $callable
     * @return static
     */
    public function apply(Functor $callable);
}
