<?php
namespace Prelude\Contract;

interface Bounded
{
    public function minBound();
    public function maxBound();
}
