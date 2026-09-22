<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Drivers\Traits;


/**
 * GD trait.
 * 
 * @since 3.1.0
 */
trait GdTrait
{


    /**
     * Fill transparent-white on selected image object.
     * 
     * @since 3.1.4
     * @param \GdImage|resource $image Image object to fill color.
     */
    protected function fillTransparentOnObject($image)
    {
        $transparentWhite = imagecolorallocatealpha($image, 255, 255, 255, 127);
        imagefill($image, 0, 0, $transparentWhite);
        imagecolortransparent($image, $transparentWhite);
        unset($transparentWhite);
    }// fillTransparentWhiteOnObject


    /**
     * Fill white background on destination image object.
     * 
     * Create temporary canvas, fill white background, copy image from "destination image object" to temp canvas, then mark this canvas as "destination image object".
     */
    protected function fillWhiteBgOnDestination()
    {
        // create temporary canvas.
        $tempCanvas = imagecreatetruecolor($this->Gd->destination_image_width, $this->Gd->destination_image_height);

        // set white color and fill it.
        $white = imagecolorallocate($tempCanvas, 255, 255, 255);
        imagefill($tempCanvas, 0, 0, $white);
        unset($white);

        // copy image to temp canvas.
        imagecopy($tempCanvas, $this->Gd->destination_image_object, 0, 0, 0, 0, $this->Gd->destination_image_width, $this->Gd->destination_image_height);

        // set image object from temp canvas.
        $this->Gd->destination_image_object = $tempCanvas;

        // don't `imagedestroy()` temporary canvas because it will be destroy both variables and cause the errors.
        unset($tempCanvas);
    }// fillWhiteBgOnDestination


    /**
     * Get image color with alpha value (transparency).
     * 
     * @since 3.2.0
     * @param string $colorName The color name. Supported 'black', 'white', 'red', 'green', 'blue', 'yellow', 'cyan', 'magenta'.
     * @param int $alpha The alpha value.
     * @param \GdImage|resource $image The GD image object or resource.
     * @throws \InvalidArgumentException Throw exception if provide argument type mismatch.
     */
    private function getImageColorAlpha($colorName, $alpha, $image)
    {
        if (!is_string($colorName)) {
            throw new \InvalidArgumentException('The argument `$colorName` must be string.');
        }
        
        if (!is_numeric($alpha)) {
            throw new \InvalidArgumentException('The argument `$alpha` must be integer.');
        }

        if (!$this->isResourceOrGDObject($image)) {
            throw new \InvalidArgumentException('The argument `$image` must be GdImage.');
        }

        switch ($colorName) {
            case 'black':
                return imagecolorallocatealpha($image, 0, 0, 0, $alpha);
            case 'red':
                return imagecolorallocatealpha($image, 255, 0, 0, $alpha);
            case 'green':
                return imagecolorallocatealpha($image, 0, 255, 0, $alpha);
            case 'blue':
                return imagecolorallocatealpha($image, 0, 0, 255, $alpha);
            case 'yellow':
                return imagecolorallocatealpha($image, 255, 255, 0, $alpha);
            case 'cyan':
                return imagecolorallocatealpha($image, 0, 255, 255, $alpha);
            case 'magenta':
                return imagecolorallocatealpha($image, 255, 0, 255, $alpha);
            case 'white':
            default:
                return imagecolorallocatealpha($image, 255, 255, 255, $alpha);
        }
    }// getImageColorAlpha


    /**
     * Check if image variable is resource of GD or is object of `\GdImage` (PHP 8.0) or not.
     *
     * @since 3.0.2 Moved from `\Rundiz\Image\Drivers\Gd`
     * @param mixed $image The `\GdImage` variable to check.
     * @return bool Return `true` if it is resource or instance of `\GdImage`, return `false` if it is not.
     */
    protected function isResourceOrGDObject($image)
    {
        return (
            (is_resource($image) && strtolower(get_resource_type($image)) === 'gd') ||
            (is_object($image) && $image instanceof \GdImage)
        );
    }// isResourceOrGDObject


    /**
     * Setup source image object (GD) from file.
     * 
     * @since 3.1.4
     * @param string $file Source image file path.
     * @param int $imageType Image type as defined in `IMAGETYPE_XXX` by PHP. This value may have got from `getImageFileData()` method.
     * @return \GdImage|resource|false Return resource or `\GdImage` (for PHP 8.0+) on success, `false` on failure.
     */
    protected function setupSourceFromFile($file, $imageType)
    {
        if (IMAGETYPE_AVIF === $imageType) {
            if (function_exists('imagecreatefromavif')) {
                return @imagecreatefromavif($file);
            }
        } elseif (IMAGETYPE_GIF === $imageType) {
            return imagecreatefromgif($file);
        } elseif (IMAGETYPE_JPEG === $imageType) {
            return imagecreatefromjpeg($file);
        } elseif (IMAGETYPE_PNG === $imageType) {
            return imagecreatefrompng($file);
        } elseif (IMAGETYPE_WEBP === $imageType) {
            $WebP = new \Rundiz\Image\Extensions\WebP($file);
            if ($WebP->isGDSupported()) {
                // if GD supported this WEBP.
                return imagecreatefromwebp($file);
            }// endif; GD supported.
            unset($WebP);
        }

        return false;
    }// setupSourceFromFile


}
