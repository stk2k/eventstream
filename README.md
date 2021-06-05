PHP simple pub-sub library
=======================

[![Latest Version on Packagist](https://img.shields.io/packagist/v/stk2k/eventstream.svg?style=flat-square)](https://packagist.org/packages/stk2k/eventstream)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://travis-ci.org/stk2k/eventstream.svg?branch=master)](https://travis-ci.org/stk2k/eventstream)
[![Coverage Status](https://coveralls.io/repos/github/stk2k/eventstream/badge.svg?branch=master)](https://coveralls.io/github/stk2k/eventstream?branch=master)
[![Code Climate](https://codeclimate.com/github/stk2k/eventstream/badges/gpa.svg)](https://codeclimate.com/github/stk2k/eventstream)
[![Total Downloads](https://img.shields.io/packagist/dt/stk2k/eventstream.svg?style=flat-square)](https://packagist.org/packages/stk2k/eventstream)

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

### 01. event source

```php
use stk2k\eventstream\EventStream;
use stk2k\eventstream\EventSourceInterface;
use stk2k\eventstream\emitter\SimpleEventEmitter;
use stk2k\eventstream\exception\EventSourceIsNotPushableException;
use Stk2k\EventStream\Event;

class NumberEventSource implements EventSourceInterface
{
    protected $numbers;
    
    public function __construct() {
        $this->numbers = ['one', 'two', 'three'];
    }
    public function canPush() : bool {
        return false;
    }
    public function push(Event $event) : EventSourceInterface {
        return $this;
    }
    public function next() {
        $number = array_shift($this->numbers);
        return $number ? new Event('number',$number) : null;
    }
}
  
// create event stream and setup callback, then flush all events
(new EventStream())
    ->channel('my channel', new NumberEventSource(), new SimpleEventEmitter())
    ->listen('number', function(Event $e){
        echo 'received number='.$e->getPayload(), PHP_EOL;
    })
    ->flush();

      
// received number=one
// received number=two
// received number=three
  
// you can not push event to unpushable event source
try{
    (new NumberEventSource())->push(new Even('number','four'));   // throws EventSourceIsNotPushableException
}
catch(EventSourceIsNotPushableException $e){
    echo 'Event not publishable.';
}
  
class PushableNumberEventSource extends NumberEventSource
{
    public function canPush() : bool {
        return true;
    }
    public function push(Event $event) : EventSourceInterface {
        if ($event->getName() === 'number'){
            $this->numbers[] = $event->getPayload();
        }
        return $this;
    }
}
  
// you acn push event to pushable event source
try{
    (new EventStream())
        ->channel('my channel')
        ->source((new PushableNumberEventSource())->push('number','four'))
        ->emitter(new SimpleEventEmitter())
        ->listen('number', function(Event $e){
                echo 'received number='.$e->getPayload(), PHP_EOL;
            })
        ->flush()
        ->push('number', 'five')
        ->flush();
}
catch(EventSourceIsNotPushableException $e){
    echo 'Event not publishable.';
}
  
// received number=one
// received number=two
// received number=three
// received number=four
// received number=five

```

## Usage

1. create event stream object
2. define your own event source class and attach new instance to stream.
3. define your own event emitter or use bundled emitter classes and attach new instance to stream.
4. define your own callback(s) and attach it(them) to stream or emitter.
5. flush events in event source via EventStream#flush() method.

## More Examples

- numbers.php: simple emitter and source sample
- multi_channel.php: listen different channel(event)s.
- regular_expression.php: single bind but listen multi channels by regular expression.
- wild_card.php: single bind but listen multi channels by wild card.

## Requirement

PHP 7.2 or later

## Installing stk2k/eventstream

The recommended way to install Calgamo/Bench is through
[Composer](http://getcomposer.org).

```bash
composer require stk2k/eventstream
```

After installing, you need to require Composer's autoloader:

```php
require 'vendor/autoload.php';
```

## License
[MIT](https://github.com/stk2k/eventstream/blob/master/LICENSE)

## Author

[stk2k](https://github.com/stk2k)
