<?php
namespace Haskel\Component\Mutex\Mutex;

interface Mutex
{
    /**
     * @return string
     */
    public function getKey();

    /**
     * @return array
     */
    public function getContext();

}