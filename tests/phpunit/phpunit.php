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

require dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';