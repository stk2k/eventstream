PHP simple pub-sub library
=======================

You can use this library to build an application using simple pub-sub pattern.


```php

require dirname(__FILE__) . '/vendor/autoload.php';
 
use EventStream\EventStream;
use EventStream\IEventSource;
use EventStream\Emitter\SimpleEventEmitter;
  
class NumberEventSource implements IEventSource
{
    protected $numbers;
    
    public function __construct() {
        $this->numbers = array('one', 'two', 'three');
    }
    public function canPush($event) {
        // you can not append event
        return false;
    }
    public function push($event, $args=null) {
        // do nothing
        return $this;
    }
    public function next() {
        $number = array_shift($this->numbers);
        return $number ? array('number',$number) : null;
    }
}
  
// create event stream and setup callback, then flush all events
(new EventStream())
    ->source(new NumberEventSource())
    ->emitter(new SimpleEventEmitter())
    ->listen('number', function($n){
            echo 'received number='.$n, PHP_EOL;
        })
    ->flush();
echo PHP_EOL;
      
// received number=one
// received number=two
// received number=three
  
// you can not push event to unpushable event source
(new NumberEventSource())->push('number','four');   // throws EventSourceIsNotPushableException
  
class PushableNumberEventSource extends NumberEventSource
{
    public function canPush($event) {
        // you can append an event
        return true;
    }
    public function push($event, $args=null) {
        if ($event==='number'){
            $this->numbers[] = $args;
        }
        return $this;
    }
}
  
// you acn push event to pushable event source
(new EventStream())
    ->source((new PushableNumberEventSource())->push('number','four'))
    ->emitter(new SimpleEventEmitter())
    ->listen('number', function($n){
            echo 'received number='.$n, PHP_EOL;
        })
    ->flush();
echo PHP_EOL;
  
// received number=one
// received number=two
// received number=three
// received number=four

```

## Installing Eventstream

The recommended way to install this library is through
[Composer](http://getcomposer.org).

```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php
```

Next, run the Composer command to install the latest stable version:

```bash
composer.phar require stk2k/eventstream
```

Or

```bash
composer require stk2k/eventstream
```

After installing, you need to require composer's autoloader:

```php
require 'vendor/autoload.php';
```

You can then later update stk2k/eventstream by using composer-update command:

 ```bash
composer.phar update
 ```

Or

```bash
composer update
```
