<?php
namespace Prelude\Contract;

interface FoldableMonoid extends Foldable, Monoid
{
    public function fold();
    public function foldMap(callable $callable);
}
