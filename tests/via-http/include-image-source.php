<?php
$source_image_jpg = '../source-images/city-amsterdam.jpg';
$source_image_png = '../source-images/city-amsterdam.png';
$source_image_pngnt = '../source-images/city-amsterdam-non-transparent.png';
$source_image_gif = '../source-images/city-amsterdam.gif';
$source_image_animated_gif = '../source-images/city-amsterdam-animated.gif';
$source_image_webp = '../source-images/city-amsterdam.webp';
$source_image_fake = '../source-images/city-amsterdam-jpg.png';
$source_image_fake2 = '../source-images/city-amsterdam-text.jpg';
$source_image_404 = '../source-images/city-amsterdam.404';

$test_data_set = [
    'JPG' => [
        'source_image_path' => $source_image_jpg,
    ],
    'PNG' => [
        'source_image_path' => $source_image_png,
    ],
    'GIF' => [
        'source_image_path' => $source_image_gif,
    ],
    'WEBP' => [
        'source_image_path' => $source_image_webp,
    ],
];
$test_data_pngnt = [
    'Non transparent PNG' => [
        'source_image_path' => $source_image_pngnt,
    ],
];
$test_data_falsy = [
    'Wrong image extension' => [
        'source_image_path' => $source_image_fake,
    ],
    'Fake image' => [
        'source_image_path' => $source_image_fake2,
    ],
    'Not exists image' => [
        'source_image_path' => $source_image_404,
    ],
];
$test_data_gifa = [
    'GIF Animation' => [
        'source_image_path' => $source_image_animated_gif,
    ],
];
