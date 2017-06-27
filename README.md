PHP simple pub-sub library
=======================

## Description

You can use this library to build an application using simple pub-sub pattern.
This library consists of three main objects below: 

- EventStream

event stream is frontend facade of this system. you can push events to event source, register event listeners to event emitter,
and flush events.

- EventSource

event source is an event provider. it has a role to provide events when event stream request.
you have to declare event source, and call EventStream#source() method to attach source.
 
- EventEmitter

event emitter is an event dispatcher. it has a role to manage user callback functions.
use Emitter/SimpleEventEmitter class for normal use, and pass it to EventStream#emitter() method.

## Demo

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
 
// eventstream can be creted with source and emitter
new EventStream(new NumberEventSource(), new SimpleEventEmitter());
 
```

## Usage

1. create event stream object
2. define your event source class and attach new instance to stream.
3. define your event emitter or use bundled Emitter/SimpleEventEmitter class, and attach new instance to stream.
4. define your callback and attach to stream or emitter.
5. flush events in event source by calling EventStream#flush() method.

## Requirement

PHP 5.3 or later

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
## License
[MIT](https://github.com/stk2k/eventstream/blob/master/LICENSE)

## Author

[stk2k](https://github.com/stk2k)
