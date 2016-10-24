<?php
namespace Prelude\Contract;

interface Arrayable
{
    public function toArray();
    public function fromArray(array $array);
}