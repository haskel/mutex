<?php
namespace Haskel\Component\Mutex;

use Haskel\Component\Mutex\Adapter\Adapter;
use Haskel\Component\Mutex\Exception\AcquireException;
use Haskel\Component\Mutex\Mutex\ExpiringMutex;
use Haskel\Component\Mutex\Mutex\Mutex;
use Closure;
use Exception;
use Haskel\Component\Mutex\Mutex\SelfReleasableMutex;
use Psr\Log\LoggerInterface;

class MutexManager
{
    /**
     * How long time wait for the next attempt to acquire the lock
     *
     * @var int
     */
    private $acquireAttemptPeriod = 1;

    /**
     * @var Adapter
     */
    private $storageAdapter;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    private $acquiredMutexList = [];

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param Adapter $storageAdapter
     * @todo: move to constructor arguments
     */
    public function setAdapter(Adapter $storageAdapter)
    {
        $this->storageAdapter = $storageAdapter;
    }

    /**
     * @param Mutex $mutex
     *
     * @return string
     */
    private function getLockKey(Mutex $mutex)
    {
        $classHash = md5(strtolower(get_class($mutex)));

        return "{$classHash}_{$mutex->getKey()}";
    }

    /**
     * @param Mutex $mutex
     * @param int   $waitTimeout
     */
    public function acquire(Mutex $mutex, $waitTimeout = 0)
    {
        $action = function () use ($mutex) {
            $lockKey = $this->getLockKey($mutex);
            // @todo: make it atomic
            if ($this->storageAdapter->exists($lockKey)) {
                throw new AcquireException("key '{$lockKey}' already acquired");
            }
            if ($mutex instanceof SelfReleasableMutex) {
                $mutex->setManager($this);
            }
            $this->storageAdapter->create($lockKey, $mutex->getContext());
            $mutexId = spl_object_hash($mutex);
            $this->acquiredMutexList[$mutexId] = $mutex;
        };
        $action->bindTo($this);

        $attemptsCount = floor($waitTimeout / $this->acquireAttemptPeriod);
        $attemptsCount = $attemptsCount ?: 1;
        $this->attempt($action, $attemptsCount, $this->acquireAttemptPeriod);
    }

    /**
     * @param Mutex $mutex
     */
    public function release(Mutex $mutex)
    {
        $lockKey = $this->getLockKey($mutex);
        $mutexId = spl_object_hash($mutex);
        if ($this->storageAdapter->exists($lockKey) && isset($this->acquiredMutexList[$mutexId])) {
            $this->storageAdapter->delete($lockKey);
        }
    }

    /**
     * @param Mutex $mutex
     *
     * @return mixed
     */
    public function isAcquired(Mutex $mutex)
    {
        $lockKey = $this->getLockKey($mutex);

        return $this->storageAdapter->exists($lockKey);
    }

    /**
     * @param Closure $action
     * @param int     $attemptsCount
     *
     * @return bool
     */
    private function attempt(Closure $action, $attemptsCount = 1, $waitSeconds = 1)
    {
        if ($waitSeconds < 1) {
            $waitSeconds = 1;
        }

        foreach (range(1, $attemptsCount) as $attemptNumber) {
            try {
                $action();
                return true;
            } catch (Exception $e) {
                $this->logger->error(sprintf("attempt #%d: %s", $attemptNumber, $e->getMessage()));
            }
            sleep((int) $waitSeconds);
        }
    }

    public function isExpired(ExpiringMutex $mutex)
    {

    }

    public function whoAcquired(Mutex $mutex)
    {

    }
}