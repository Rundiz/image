# Image manipulation

Image manipulation use GD or Imagick as drivers. 

## Features:
### File extensions supported

* GIF
* JPG (JPEG)
* PNG
* WEBP `*`

### Functional

* Crop
* Flip (Require PHP 5.5+ for GD driver.)
* Resize (aspect ratio and not)
* Rotate
* Watermark image (including alpha transparency.)
* Watermark text (including alpha transparency.)
* Supported transparent GIF, PNG.
* Supported animated GIF (Imagick only).

[![Latest Stable Version](https://poser.pugx.org/rundiz/image/v/stable)](https://packagist.org/packages/rundiz/image)
[![License](https://poser.pugx.org/rundiz/image/license)](https://packagist.org/packages/rundiz/image)
[![Total Downloads](https://poser.pugx.org/rundiz/image/downloads)](https://packagist.org/packages/rundiz/image)

## Example
### Gd driver

```php
$Image = new \Rundiz\Image\Drivers\Gd('/path/to/source-image.jpg');
$Image->resize(900, 600);
$Image->save('/path/to/new-file-name.jpg');
```
### Imagick driver

```php
$Image = new \Rundiz\Image\Drivers\Imagick('/path/to/source-image.jpg');
$Image->resize(900, 600);
$Image->save('/path/to/new-file-name.jpg');
```

### Fallback drivers
You can use multiple drivers as fallback if it does not support.

```php
if (extension_loaded('imagick') === true) {
    $Image = new \Rundiz\Image\Drivers\Imagick('/path/to/source-image.jpg');
} else {
    $Image = new \Rundiz\Image\Drivers\Gd('/path/to/source-image.jpg');
}
$Image->rotate('hor');
$Image->crop(500, 500, 'center', 'middle');
$Image->save('/path/to/new-file-name.jpg');
```

### Image manipulation methods
```php
// crop an image.
$Image->crop(400, 400, 'center', 'middle');// crop start from center of X and Y
$Image->crop(400, 400, 20, 90);// crop start from X 20 and Y 90

// resize
$Image->resize(600, 400);
// resize without aspect ratio
$Image->resizeNoRatio(500, 300);

// rotate
$Image->rotate();// 90 degree
$Image->rotate(180);
$Image->rotate(270);
$Image->rotate('hor');// flip horizontal
$Image->rotate('vrt');// flip vertical
$Image->rotate('horvrt');// flip horizontal and vertical

// watermark image
$Image->watermarkImage('/var/www/image/watermark.png', 'center', 'middle');
$Image->watermarkImage('/var/www/image/watermark.png', 50, 90);// watermark start from X 50 and Y 90

// watermark text
$Image->watermarkText('hello world', '/var/www/fonts/myfont.ttf', 'center', 'middle', 16);
$Image->watermarkText('hello world', '/var/www/fonts/myfont.ttf', 50, 90, 16);// watermark start from X 50 and Y 90
```

#### Multiple image process
```php
$Image = new \Rundiz\Image\Drivers\Gd('/path/to/source-image.jpg');
$Image->resize(900, 600);
$Image->save('/path/to/new-file-name-900x600.jpg');
// use method clear() to clear all processed data and start new image process with the same image source.
$Image->clear();
$Image->resize(300, 100);
$Image->save('/path/to/new-file-name-300x100.jpg');
```

For more details, please look in tests folder or see [API doc][1]

---
Remark:

* `*` WEBP<br>
    There are known bugs ([1][oldgdwebpbug], [2][oldgdwebpbug2]) that prior PHP 7.0, the transparent PNG or GIF that converted to WEBP will be filled with the background color.

[1]: http://apidocs.rundiz.com/image/
[oldgdwebpbug]: https://github.com/rosell-dk/webp-convert/issues/238#issuecomment-545928597
[oldgdwebpbug2]: https://stackoverflow.com/a/58543717/128761