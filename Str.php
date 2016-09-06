<?php
namespace Prelude;

use IteratorAggregate;

class Str impelments IteratorAggregate {

    protected $value;
    
    public function _construct($value)
    {
        $this->value = $value
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
