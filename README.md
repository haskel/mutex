Mutex Component
==================

The Mutex component provides the ability to manage locks while accessing the resource at the same time.

## Installation
```bash
composer require haskel/mutex
```

## Usage
Basic usage
```php
use Haskel\Component\Mutex\MutexManager;
use Haskel\Component\Mutex\Mutex\PlainMutex;

$mutexManager = new MutexManager();
$mutex = new PlainMutex('SOME_UNIQUE_STRING');
$mutexManager->acquire($mutex);
//some actions
$mutexManager->release($mutex);
```

<br><br>
To release everytime if exception throws
```php
use Haskel\Component\Mutex\MutexManager;
use Haskel\Component\Mutex\Mutex\PlainMutex;

$mutexManager = new MutexManager();
$mutex = new PlainMutex('SOME_UNIQUE_STRING');
$mutexManager->acquire($mutex);
//some actions
try {
    //some actions
} finally {
    $mutexManager->release($mutex);
}

```

Try to acquire within 20 seconds
```php
use Haskel\Component\Mutex\MutexManager;
use Haskel\Component\Mutex\Mutex\PlainMutex;

$mutexManager = new MutexManager();
$mutex = new PlainMutex('SOME_UNIQUE_STRING');
$mutexManager->acquire($mutex, 20);
// .......
```
