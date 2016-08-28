<?php
namespace Prelude\Contract;

interface Applicative extends Functor
{
    public function apply(Functor $callable);
}
