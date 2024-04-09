<?php
$source_image_avif = '../source-images/source-image.avif';
$source_image_jpg = '../source-images/source-image.jpg';
$source_image_png = '../source-images/source-image.png';
$source_image_gif = '../source-images/source-image.gif';
$source_image_animated_gif = '../source-images/source-image-animated.gif';
$source_image_animated_webp = '../source-images/source-image-animated.webp';
$source_image_webp = '../source-images/source-image.webp';
$source_image_fake = '../source-images/source-image-jpg.png';
$source_image_fake2 = '../source-images/source-image-text.jpg';
$source_image_404 = '../source-images/source-image.404';

$test_data_set = [
    'JPG' => [
        'source_image_path' => $source_image_jpg,
    ],
    'PNG' => [
        'source_image_path' => $source_image_png,
    ],
    'AVIF' => [
        'source_image_path' => $source_image_avif,
    ],
    'GIF' => [
        'source_image_path' => $source_image_gif,
    ],
    'WEBP' => [
        'source_image_path' => $source_image_webp,
    ],
];
$test_data_falsy = [
    'Wrong image extension' => [
        'source_image_path' => $source_image_fake,
    ],
    'Fake image' => [
        'source_image_path' => $source_image_fake2,
    ],
    'Image not exists' => [
        'source_image_path' => $source_image_404,
    ],
];
$test_data_anim = [
    'GIF Animation' => [
        'source_image_path' => $source_image_animated_gif,
    ],
    'WEBP Animation' => [
        'source_image_path' => $source_image_animated_webp,
    ],
];

$saveAsExts = ['avif', 'gif', 'jpg', 'png', 'webp'];
