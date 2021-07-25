<?php
require dirname(__DIR__).'/phpunit/Autoload.php';
$Autoload = new \Rundiz\Image\Tests\Autoload();
$Autoload->addNamespace('Rundiz\\Image', dirname(dirname(__DIR__)).'/Rundiz/Image');
$Autoload->register();