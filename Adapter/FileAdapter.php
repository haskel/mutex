<?php
namespace Haskel\Component\Mutex\Adapter;

use Haskel\Component\Mutex\Exception\AdapterException;

class FileAdapter implements Adapter
{
    /**
     * @var string
     */
    private $filesDir;

    /**
     * @var string
     */
    private $hashAlgo;

    /**
     * @param string      $filesDir
     * @param string|null $hashAlgo
     */
    public function __construct($filesDir, $hashAlgo = null)
    {
        $this->filesDir = $filesDir;
        $this->hashAlgo = $hashAlgo ?: 'sha256';
        $this->checkFilesDir($filesDir);
    }

    /**
     * @param $lockKey
     *
     * @return string
     */
    private function getFileName($lockKey)
    {
        return $this->filesDir . hash($this->hashAlgo, $lockKey) . ".lock";
    }

    /** {@inheritdoc} */
    public function create($lockKey, $context)
    {
        $file = $this->getFileName($lockKey);
        if (file_exists($file)) {
            throw new AdapterException("Lock file already exists. Lock key: {$lockKey}");
        }
        file_put_contents($file, json_encode($context), LOCK_EX);
    }

    /** {@inheritdoc} */
    public function delete($lockKey)
    {
        $file = $this->getFileName($lockKey);
        unlink($file);
    }

    /** {@inheritdoc} */
    public function exists($lockKey)
    {
        $file = $this->getFileName($lockKey);

        return file_exists($file);
    }

    /** {@inheritdoc} */
    public function get($lockKey)
    {
        $file = $this->getFileName($lockKey);
        if (!file_exists($file)) {
            throw new AdapterException("Lock file does not exist. Lock key: {$lockKey}");
        }
        $data = file_get_contents($file);

        return json_encode($data, true);
    }

    /**
     * @param $filesDir
     *
     * @throws AdapterException
     */
    public function checkFilesDir($filesDir)
    {
        if (!file_exists($filesDir)) {
            mkdir($filesDir, 0755, true);
        }

        if (!is_writable($filesDir)) {
            throw new AdapterException("Directory {$filesDir} is not available to write");
        }
    }
}