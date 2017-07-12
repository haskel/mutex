<?php
namespace Haskel\Component\Mutex\Adapter;

use Haberberger\RedisStorageBundle\Services\RedisStorage;

class RedisAdapter implements Adapter
{
    /**
     * @var RedisStorage
     */
    private $redis;

    /**
     * @var string
     */
    private $keyPrefix;

    /**
     * @param RedisStorage $redis
     */
    public function __construct(RedisStorage $redis, $keyPrefix = '')
    {
        $this->redis     = $redis;
        $this->keyPrefix = $keyPrefix;
    }

    /**
     * @param $lockKey
     *
     * @return string
     */
    private function getStorageKey($lockKey)
    {
        return ($this->keyPrefix) ? $this->keyPrefix . ":" . $lockKey : $lockKey;
    }

    /** {@inheritdoc} */
    public function create($lockKey, $context)
    {
        $storageKey = $this->getStorageKey($lockKey);
        $contextString = json_encode($context);
        $this->redis->stringWrite($storageKey, $contextString);
    }

    /** {@inheritdoc} */
    public function delete($lockKey)
    {
        $storageKey = $this->getStorageKey($lockKey);
        if ($this->redis->stringExists($storageKey)) {
            $this->redis->stringDelete($storageKey);
        }
    }

    /** {@inheritdoc} */
    public function exists($lockKey)
    {
        $storageKey = $this->getStorageKey($lockKey);
        return (bool) $this->redis->stringExists($storageKey);
    }

    /** {@inheritdoc} */
    public function get($lockKey)
    {
        $storageKey = $this->getStorageKey($lockKey);
        if ($this->redis->stringExists($storageKey)) {
            $contextString = $this->redis->stringRead($storageKey);

            return json_decode($contextString, true);
        }

        return null;
    }
}