<?php
namespace Haskel\Component\Mutex\Mutex;

use Haskel\Component\Mutex\MutexManager;

interface SelfReleasableMutex
{
    public function setManager(MutexManager $manager);
    public function __destruct();
}