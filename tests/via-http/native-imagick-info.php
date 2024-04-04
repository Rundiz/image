<?php

if (extension_loaded('imagick') !== true) {
    echo 'Imagick was not installed.';
    exit();
}

$imagick_extension_version = phpversion('imagick');
echo '<strong>Imagick</strong> version: ' . $imagick_extension_version . '<br>' . PHP_EOL;

$imgmgVA = \Imagick::getVersion();
if (!is_array($imgmgVA) || (is_array($imgmgVA) && !array_key_exists('versionString', $imgmgVA))) {
    $imageMagickVersion = 'UNKNOWN';
} else {
    preg_match('/ImageMagick ([0-9]+\.[0-9]+\.[0-9]+)/', $imgmgVA['versionString'], $matches);
    if (!is_array($matches) || (is_array($matches) && !array_key_exists(1, $matches))) {
        $imageMagickVersion = 'UNKNOWN';
    } else {
        $imageMagickVersion = $matches[1];
    }
    unset($matches);
}
echo '<strong>ImageMagick</strong> version: ' . $imageMagickVersion . '<br>' . PHP_EOL;
echo '<!-- ' . print_r($imgmgVA, true) . ' -->' . PHP_EOL;