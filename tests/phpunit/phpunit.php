<?php
/**
 * Requires
 * PHP 7.1+
 * PHPUnit 7.0+
 */


$requiredPhpUnitVersion = '7.0';
if (
    !class_exists('\\PHPUnit\\Runner\\Version') || 
    version_compare(\PHPUnit\Runner\Version::id(), $requiredPhpUnitVersion, '<')
) {
    die('Required PHPUnit version ' . $requiredPhpUnitVersion);
}

require __DIR__.'/Autoload.php';

$Autoload = new \Rundiz\Image\Tests\Autoload();
$Autoload->addNamespace('Rundiz\\Image\\Tests', __DIR__);
$Autoload->addNamespace('Rundiz\\Image', dirname(dirname(__DIR__)).'/Rundiz/Image');
$Autoload->register();