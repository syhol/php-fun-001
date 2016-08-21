<?php
namespace Prelude\Contract;

interface Ord extends Eq
{
    public function compare(Ord $other);
    public function lt(Ord $other);
    public function lte(Ord $other);
    public function gt(Ord $other);
    public function gte(Ord $other);
    public function max(Ord $other);
    public function min(Ord $other);
}
