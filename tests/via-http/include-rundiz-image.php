<?php
require __DIR__ . '/Autoload.php';
$Autoload = new \Rundiz\Image\Tests\HTTP\Autoload();
$Autoload->addNamespace('Rundiz\\Image', dirname(dirname(__DIR__)).'/Rundiz/Image');
$Autoload->register();