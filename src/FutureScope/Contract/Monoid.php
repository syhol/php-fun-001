<?php
namespace Prelude\Contract;

interface Monoid
{
    public function emptyValue();
    public function append($value);
    public function concat($value);
}
