<?php
namespace Prelude;

use ArrayIterator;
use IteratorAggregate;

class Str implements IteratorAggregate {

    protected $value;
    
    public function _construct($value)
    {
        $this->value = $value;
    }
    
    public function getIterator() 
    {
        return new ArrayIterator($this->chars());
    }
    
    public function chars()
    {
        return str_split($this->value);
    }    
}
