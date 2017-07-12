<?php
namespace Haskel\Component\Mutex\Adapter;

interface Adapter
{
    public function create($lockKey, $context);
    public function delete($lockKey);
    public function exists($lockKey);
    public function get($lockKey);
}