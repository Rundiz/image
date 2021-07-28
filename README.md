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

For more details, please look in tests folder

---
Remark:

* `*` WEBP<br>
    There are known bugs that prior PHP 7.0, the transparent PNG or GIF that converted to WEBP will be filled with the background color.