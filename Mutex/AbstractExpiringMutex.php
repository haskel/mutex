<?php
namespace Haskel\Component\Mutex\Mutex;

abstract class AbstractExpiringMutex
extends AbstractMutex
implements ExpiringMutex
{
    private $expiration;
    private $ttl;
}