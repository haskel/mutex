<?php
namespace Haskel\Component\Mutex\Mutex;

use Haskel\Component\Mutex\MutexManager;

abstract class AbstractMutex implements Mutex, SelfReleasableMutex
{
    /**
     * @var MutexManager
     */
    protected $mutexManager;

    /** {@inheritdoc} */
    abstract public function getKey();

    /** {@inheritdoc} */
    public function getContext()
    {
        $trace = debug_backtrace();

        return $trace[0];
    }

    /** {@inheritdoc} */
    public function setManager(MutexManager $manager)
    {
        $this->mutexManager = $manager;
    }

    /** {@inheritdoc} */
    public function __destruct()
    {
        $this->mutexManager->release($this);
    }
}