<?php
namespace Prelude;

class NamespaceAccessor
{
    public $namespace;
    
    public function __construct($namespace)
    {
        $this->namespace = $namespace;
    }
    
    public function __call($function, $args) {
        return call_user_func_array($this->namespace . '\' . $function, $args);
    }
}
