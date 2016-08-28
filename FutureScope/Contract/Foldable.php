<?php
namespace Prelude\Contract;

interface Foldable extends \Countable, Arrayable
{
    public function foldr(callable $callable, $initial);
    public function foldl(callable $callable, $initial);
    public function null();
    public function elem($element);
    public function maximum();
    public function minimum();
    public function sum();
    public function product();
}
