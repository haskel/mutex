<?php
namespace Haskel\Component\Mutex\Mutex;

use Haskel\Component\Mutex\Exception\MutexCreateException;

class PlainMutex extends AbstractMutex
{
    /**
     * @var string
     */
    private $key;

    /**
     * @param $key
     *
     * @throws MutexCreateException
     */
    public function __construct($key)
    {
        if (!is_string($key) || strlen(trim($key)) == 0) {
            throw new MutexCreateException('Key must be a string');
        }

        $this->key = $key;
    }

    /** {@inheritdoc} */
    public function getKey()
    {
        return $this->key;
    }
}