<?php
namespace Prelude\Contract;

interface Sliceable
{
    public function takeWhere();
    public function takeWhile();
    public function takeUntil();
    public function takeStart();
    public function takeEnd();

    public function dropWhere();
    public function dropWhile();
    public function dropUntil();
    public function dropStart();
    public function dropEnd();
}
