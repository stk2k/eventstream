<?php
$base_dir = dirname(dirname(__DIR__));
require_once $base_dir . '/vendor/autoload.php';

spl_autoload_register(function($class) use($base_dir){
    if (strpos($class, 'Stk2k\\EventStream\\') === 0) {
        $name = substr($class, strlen('Stk2k\\EventStream\\'));
        $file = $base_dir . '/src/' . str_replace('\\', '/', $name) . '.php';
        require_once $file;
    }
});
