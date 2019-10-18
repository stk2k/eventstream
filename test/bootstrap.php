<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

spl_autoload_register(function($class) {
    if (strpos($class, 'Stk2k\\EventStream\\') === 0) {
        $dir = strcasecmp(substr($class, -4), 'Test') ? 'src/' : 'test/';
        $name = substr($class, strlen('Stk2k\\EventStream\\'));
        $file = dirname(__DIR__) . '/' . $dir . str_replace('\\', '/', $name) . '.php';
        require_once $file;
    }
});
