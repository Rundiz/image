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
     * Fill transparent to the destination image.
     */
    protected function fillTransparentDestinationImage()
    {
        // create temp canvas.
        $tempCanvas = imagecreatetruecolor($this->Gd->destination_image_width, $this->Gd->destination_image_height);

        // set temp canvas to be transparent.
        imagesavealpha($tempCanvas, true);
        $TCColorForTransparent = imagecolorallocatealpha($tempCanvas, 255, 0, 255, 127);
        imagefill($tempCanvas, 0, 0, $TCColorForTransparent);
        imagecolortransparent($tempCanvas, $TCColorForTransparent);
        unset($TCColorForTransparent);

        // copy image to temp canvas.
        imagecopy($tempCanvas, $this->Gd->destination_image_object, 0, 0, 0, 0, $this->Gd->destination_image_width, $this->Gd->destination_image_height);

        // set image object from temp canvas.
        $this->Gd->destination_image_object = $tempCanvas;

        // don't destroy temporary canvas because it will be errors.
        unset($tempCanvas);
    }// fillTransparentDestinationImage


    /**
     * Fill white to the destination image.
     */
    protected function fillWhiteDestinationImage()
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

        // don't destroy temporary canvas because it will be errors.
        unset($tempCanvas);
    }// fillWhiteDestinationImage


    /**
     * Check if image variable is resource of GD or is object of `\GDImage` (PHP 8.0) or not.
     *
     * @since 3.0.2 Moved from `\Rundiz\Image\Drivers\Gd`
     * @param mixed $image
     * @return bool Return `true` if it is resource or instance of `\GDImage`, return `false` if it is not.
     */
    protected function isResourceOrGDObject($image)
    {
        return (
            (is_resource($image) && get_resource_type($image) === 'gd') ||
            (is_object($image) && $image instanceof \GDImage)
        );
    }// isResourceOrGDObject


}
