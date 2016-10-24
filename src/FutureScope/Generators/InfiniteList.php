<?php
namespace Prelude\Generators;

use Generator;
use Prelude\Contract\Monad;
use Prelude\Contract\Functor;

class InfiniteList implements Monad, \Iterator
{
    /**
     * @var int
     */
    private $position = 0;

    /**
     * @var array
     */
    private $cache = [];

    /**
     * @var Generator
     */
    private $generator;

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    public function apply(Functor $callable)
    {
        // TODO: Implement apply() method.
    }

    public function map(callable $callable)
    {
        $generator = function () use ($callable) {
            foreach ($this as $value) {
                yield $callable($value);
            }
        };

        new self($generator());
    }

    public function pure($value)
    {
        $generator = function () use ($value) { yield $value; };

        return new self($generator());
    }

    public function bind(callable $callable)
    {
        // TODO: Implement bind() method.
    }

    public function current()
    {
        if ( ! isset($this->cache[$this->position])) {
            $this->cache[$this->position] = $this->generator->current();
        }

        return $this->cache[$this->position];
    }

    public function next()
    {
        ++$this->position;
        $this->generator->next();
    }

    public function key()
    {
        return $this->position;
    }

    public function valid()
    {
        $this->generator->valid();
    }

    public function rewind()
    {
        --$this->position;
    }
}