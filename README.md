# Image manipulation

Image manipulation use GD or Imagick as drivers. It support watermark image or text, resize, crop, rotate, transparency gif or png and also support animation gif (Imagick only).

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