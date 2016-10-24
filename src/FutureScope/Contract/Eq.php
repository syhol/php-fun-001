<?php
namespace Prelude\Contract;

interface Eq
{
    public function equal(Eq $other);
    public function notEqual(Eq $other);
}
