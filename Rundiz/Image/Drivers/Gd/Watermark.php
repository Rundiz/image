<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Drivers\Gd;


/**
 * Watermark image and text.
 * 
 * @since 3.1.0
 */
class Watermark extends \Rundiz\Image\Drivers\AbstractGdCommand
{


    use \Rundiz\Image\Traits\CalculationTrait;


    use \Rundiz\Image\Drivers\Traits\GdTrait;


    use \Rundiz\Image\Traits\ImageTrait;


    /**
     * Apply watermark image to source image.
     * 
     * @see Rundiz\Image\ImageInterface::watermarkImage() for more details.
     * @param string $wm_img_path Full path of watermark image file
     * @param int|string $wm_img_start_x Position to begin in x axis. The value is integer or 'left', 'center', 'right'.
     * @param int|string $wm_img_start_y Position to begin in y axis. The value is integer or 'top', 'middle', 'bottom'.
     * @param array $options The watermark options. (Since v3.1.3)<br>
     *      `padding` (int) Padding around watermark object. Use with left, right, bottom, top but not middle, center. See `\Rundiz\Image\Traits\CalculationTrait::calculateWatermarkImageStartXY()`.<br>
     *      `opacity` (int) The image opacity value from 0 (full transparent) to 100 (no transparent).<br>
     * @return bool Return `true` on success, `false` on failure. Call to `status_msg` property to see the details on failure.
     */
    public function applyImage($wm_img_path, $wm_img_start_x = 0, $wm_img_start_y = 0, array $options = [])
    {
        if (false === $this->setupWatermarkImageObject($wm_img_path)) {
            return false;
        }

        $this->normalizeWatermarkOptions($options);
        list($wm_img_start_x, $wm_img_start_y) = $this->normalizeStartPosition($wm_img_start_x, $wm_img_start_y, null, null, $options);

        // copy watermark image on to source image (in this case, it is destination image object).
        $this->imagecopymergeAlphaPreserve(
            $this->Gd->destination_image_object,
            $this->Gd->watermark_image_object, 
            $wm_img_start_x, 
            $wm_img_start_y, 
            0, 
            0,
            $this->Gd->watermark_image_width, 
            $this->Gd->watermark_image_height,
            (isset($options['opacity']) ? $options['opacity'] : 100)
        );

        if ($this->isResourceOrGDObject($this->Gd->watermark_image_object) && version_compare(PHP_VERSION, '8.0', '<')) {
            // if there is watermark image object.
            imagedestroy($this->Gd->watermark_image_object);
        }
        $this->Gd->watermark_image_object = null;

        if ($this->Gd->destination_image_object == null) {
            $this->Gd->destination_image_object = $this->Gd->source_image_object;
            $this->Gd->source_image_object = null;
        }

        $this->Gd->destination_image_height = imagesy($this->Gd->destination_image_object);
        $this->Gd->destination_image_width = imagesx($this->Gd->destination_image_object);

        $this->Gd->source_image_height = $this->Gd->destination_image_height;
        $this->Gd->source_image_width = $this->Gd->destination_image_width;

        return true;
    }// applyImage


    /**
     * Merge a source image onto the destination with a forced opacity level,
     * preserving the alpha channel of both images.
     *
     * Drop-in replacement for imagecopymerge(), which ignores alpha and
     * corrupts transparency when the destination is not fully opaque.  
     * Each source pixel's own alpha is multiplied by the forced opacity,
     * then composited over the destination using source-over blending,
     * so transparent areas of the destination stay transparent.
     *
     * Both images are converted to truecolor in place if they are palette
     * based. The destination's alpha blending is disabled and its alpha
     * channel is flagged to be saved.
     *
     * @since 3.2.10
     * @param \GdImage|resource $dst Destination image, modified in place.
     * @param \GdImage|resource $src Source image to merge onto the destination.
     * @param int $dstX X coordinate in the destination to start merging at.
     * @param int $dstY Y coordinate in the destination to start merging at.
     * @param int $srcX X coordinate in the source to start reading from.
     * @param int $srcY Y coordinate in the source to start reading from.
     * @param int $width Width of the region to merge, in pixels.
     * @param int $height Height of the region to merge, in pixels.
     * @param int $opacity Forced opacity of the source, 0 (full transparent) to 100 (no transparent).
     */
    private function imagecopymergeAlphaPreserve(
        $dst,
        $src,
        $dstX,
        $dstY,
        $srcX,
        $srcY,
        $width,
        $height,
        $opacity = 100
    ) {
        if (!imageistruecolor($src)) {
            imagepalettetotruecolor($src);
        }

        if (!imageistruecolor($dst)) {
            imagepalettetotruecolor($dst);
        }

        $opacity = max(0, min(100, $opacity));
        $opacityFactor = $opacity / 100.0;

        // We are writing alpha values ourselves.
        imagealphablending($dst, false);
        imagesavealpha($dst, true);

        for ($y = 0; $y < $height; $y++) {
            $dy = $dstY + $y;
            $sy = $srcY + $y;

            for ($x = 0; $x < $width; $x++) {
                $dx = $dstX + $x;
                $sx = $srcX + $x;

                $srcPixel = imagecolorat($src, $sx, $sy);
                $dstPixel = imagecolorat($dst, $dx, $dy);

                // GD alpha: 0 = opaque, 127 = fully transparent.
                $srcA = ($srcPixel >> 24) & 0x7F;
                if ($srcA === 127) {
                    continue;
                }
                $srcR = ($srcPixel >> 16) & 0xFF;
                $srcG = ($srcPixel >> 8)  & 0xFF;
                $srcB = $srcPixel & 0xFF;

                $dstA = ($dstPixel >> 24) & 0x7F;
                $dstR = ($dstPixel >> 16) & 0xFF;
                $dstG = ($dstPixel >> 8)  & 0xFF;
                $dstB = $dstPixel & 0xFF;

                // Convert GD alpha to normal opacity: 0.0 = transparent, 1.0 = opaque.
                $srcOpacity = (1.0 - ($srcA / 127.0)) * $opacityFactor;
                $dstOpacity = 1.0 - ($dstA / 127.0);

                // Source-over compositing.
                $outOpacity = $srcOpacity + $dstOpacity * (1.0 - $srcOpacity);

                if ($outOpacity <= 0.0) {
                    $outR = 0;
                    $outG = 0;
                    $outB = 0;
                    $outA = 127;
                } else {
                    $outR = (
                        ($srcR * $srcOpacity) +
                        ($dstR * $dstOpacity * (1.0 - $srcOpacity))
                    ) / $outOpacity;

                    $outG = (
                        ($srcG * $srcOpacity) +
                        ($dstG * $dstOpacity * (1.0 - $srcOpacity))
                    ) / $outOpacity;

                    $outB = (
                        ($srcB * $srcOpacity) +
                        ($dstB * $dstOpacity * (1.0 - $srcOpacity))
                    ) / $outOpacity;

                    // Convert normal opacity back to GD alpha.
                    $outA = 127 * (1.0 - $outOpacity);
                }

                $outR = max(0, min(255, (int) round($outR)));
                $outG = max(0, min(255, (int) round($outG)));
                $outB = max(0, min(255, (int) round($outB)));
                $outA = max(0, min(127, (int) round($outA)));

                $color = imagecolorallocatealpha(
                    $dst,
                    $outR,
                    $outG,
                    $outB,
                    $outA
                );

                imagesetpixel($dst, $dx, $dy, $color);
            }// endfor;
        }// endfor;
    }// imagecopymergeAlphaPreserve


    /**
     * Apply watermark text to source image.
     * 
     * @see Rundiz\Image\ImageInterface::watermarkText() for more details.
     * @param string $wm_txt_text Watermark text
     * @param string $wm_txt_font_path 'True Type Font' path
     * @param int|string $wm_txt_start_x Position to begin in x axis. The value is integer or 'left', 'center', 'right'.
     * @param int|string $wm_txt_start_y Position to begin in x axis. The value is integer or 'top', 'middle', 'bottom'.
     * @param int $wm_txt_font_size Font size
     * @param string $wm_txt_font_color Font color. ('black', 'white', 'red', 'green', 'blue', 'yellow', 'cyan', 'magenta')
     * @param int $wm_txt_font_alpha Text transparency value from 0 (no transparent) to 127 (full transparent).
     * @param array $options The watermark text options. (Since v.3.1.0)<br>
     *              `fillBackground` (bool) Set to `true` to fill background color for text bounding box. Default is `false` to use transparent.<br>
     *              `backgroundColor` (string) The background color to fill for text bounding box. Available values are 'black', 'white', 'red', 'green', 'blue', 'yellow', 'cyan', 'magenta', 'debug'.<br>
     *              `padding` (int) (Since v3.1.3) Padding around watermark text. Use with left, right, bottom, top but not middle, center.<br>
     * @return bool Return `true` on success, `false` on failed. Call to `status_msg` property to see the details on failure.
     */
    public function applyText(
        $wm_txt_text, 
        $wm_txt_font_path, 
        $wm_txt_start_x = 0, 
        $wm_txt_start_y = 0, 
        $wm_txt_font_size = 10, 
        $wm_txt_font_color = 'white', 
        $wm_txt_font_alpha = 60,
        array $options = []
    ) {
        $wm_txt_font_path = realpath($wm_txt_font_path);

        // calculate text width and height
        // @link http://stackoverflow.com/questions/11696920/calculating-text-width-with-php-gd Original source code.
        $type_space = imagettfbbox($wm_txt_font_size, 0, $wm_txt_font_path, $wm_txt_text);
        $wm_txt_height = abs($type_space[5] - $type_space[1]) + $this->Gd->wmTextBoundingBoxYPadding;
        $wm_txt_width = abs($type_space[4] - $type_space[0]) + 5;// +5 for add bounding box space to the right. so, it don't get cut before character end and for the same space as Imagick.
        unset($type_space);

        $this->normalizeWatermarkOptions($options);
        list($wm_txt_start_x, $wm_txt_start_y) = $this->normalizeStartPosition($wm_txt_start_x, $wm_txt_start_y, $wm_txt_width, $wm_txt_height, $options);

        // begins watermark text --------------------------------------------------------------------------------------------
        // create watermark text canvas
        list($wm_txt_object, $textColor) = $this->createWatermarkTextObject($wm_txt_width, $wm_txt_height, $wm_txt_font_color, $wm_txt_font_alpha, $options);

        // write text
        // y coords below must -`wmTextBottomPadding` to allow something like p, g show full size
        imagettftext(
            $wm_txt_object, 
            $wm_txt_font_size, 
            0, 
            0, 
            ($wm_txt_height - $this->Gd->wmTextBottomPadding), 
            $textColor, 
            $wm_txt_font_path, 
            $wm_txt_text
        );
        unset($textColor);

        // copy text to image
        imagecopy($this->Gd->destination_image_object, $wm_txt_object, $wm_txt_start_x, $wm_txt_start_y, 0, 0, $wm_txt_width, $wm_txt_height);
        // end watermark text -----------------------------------------------------------------------------------------------

        if (version_compare(PHP_VERSION, '8.0', '<')) {
            imagedestroy($wm_txt_object);
        }
        unset($wm_txt_height, $wm_txt_object, $wm_txt_width);

        if ($this->Gd->destination_image_object == null) {
            $this->Gd->destination_image_object = $this->Gd->source_image_object;
            $this->Gd->source_image_object = null;
        }

        $this->Gd->destination_image_height = imagesy($this->Gd->destination_image_object);
        $this->Gd->destination_image_width = imagesx($this->Gd->destination_image_object);

        $this->Gd->source_image_height = $this->Gd->destination_image_height;
        $this->Gd->source_image_width = $this->Gd->destination_image_width;

        return true;
    }// applyText


    /**
     * Create watermark text object.
     * 
     * @see Rundiz\Image\ImageInterface::watermarkText() for more details.
     * @since 3.1.4
     * @param int $wm_txt_width
     * @param int $wm_txt_height
     * @param string $wm_txt_font_color
     * @param int $wm_txt_font_alpha
     * @param array $options
     * @return array Return indexed array:<br>
     *      index 0 (resource|\GdImage|false) Resource or object of text canvas.<br>
     *      index 1 (object) Color identifier.<br>
     */
    private function createWatermarkTextObject(
        $wm_txt_width, 
        $wm_txt_height, 
        $wm_txt_font_color, 
        $wm_txt_font_alpha, 
        array $options = []
    ) {
        $wm_txt_object = imagecreatetruecolor($wm_txt_width, $wm_txt_height);
        if (function_exists('imageresolution')) {
            imageresolution($wm_txt_object, 96, 96);
        }
        imagealphablending($wm_txt_object, false);
        imagesavealpha($wm_txt_object, true);

        // check watermark text font alpha must be 0-127
        $wm_txt_font_alpha = intval($wm_txt_font_alpha);
        if ($wm_txt_font_alpha < 0 || $wm_txt_font_alpha > 127) {
            $wm_txt_font_alpha = 60;
        }

        if ('transwhitetext' === $wm_txt_font_color) {
            // if font color is `'transwhitetext'`. set to white.
            // @todo remove this in v4.0
            $wm_txt_font_color = 'white';
        }

        // set default bg color
        $fillWmBg = $this->getImageColorAlpha('white', 127, $wm_txt_object);

        if (isset($options['fillBackground']) && $options['fillBackground'] === true) {
            if (isset($options['backgroundColor'])) {
                $backgroundColor = $options['backgroundColor'];
                $backgroundAlpha = $options['backgroundAlpha'];
                if (strtolower($backgroundColor) === 'colordebugbg' || strtolower($backgroundColor) === 'debug') {
                    $fillWmBg = $this->getImageColorAlpha('blue', 85, $wm_txt_object);
                } else {
                    $fillWmBg = $this->getImageColorAlpha($backgroundColor, $backgroundAlpha, $wm_txt_object);
                }
                unset($backgroundAlpha, $backgroundColor);
            }
        }

        // fill background color
        imagefill($wm_txt_object, 0, 0, $fillWmBg);
        unset($fillWmBg);

        // re-enable blending so text anti-aliasing blends into the background.
        // the result will be look the same as using `Imagick`.
        if (isset($options['fillBackground']) && $options['fillBackground'] === true) {
            imagealphablending($wm_txt_object, true);
        }

        $textColor = $this->getImageColorAlpha($wm_txt_font_color, $wm_txt_font_alpha, $wm_txt_object);

        return [$wm_txt_object, $textColor];
    }// createWatermarkTextObject


    /**
     * Normalize start X and Y position.
     * 
     * @since 3.1.4
     * @param int|string $wm_img_start_x
     * @param int|string $wm_img_start_y
     * @param int|null $watermark_width Watermark width. Set to `null` to automatically get it from image object.
     * @param int|null $watermark_height Watermark height. Set to `null` to automatically get it from image object.
     * @param array $options
     * @return array Return indexed array where first array is start X, second is start Y.
     */
    private function normalizeStartPosition($wm_img_start_x, $wm_img_start_y, $watermark_width = null, $watermark_height = null, array $options = [])
    {
        // if start x or y is number, convert to integer value
        if (is_numeric($wm_img_start_x)) {
            $wm_img_start_x = intval($wm_img_start_x);
        }
        if (is_numeric($wm_img_start_y)) {
            $wm_img_start_y = intval($wm_img_start_y);
        }

        if (is_null($watermark_width)) {
            $watermark_width = $this->Gd->watermark_image_width;
        }
        if (is_null($watermark_height)) {
            $watermark_height = $this->Gd->watermark_image_height;
        }

        // if start x or y is NOT number, calculate the real position of start x or y from word left, center, right, top, middle, bottom
        if (!is_numeric($wm_img_start_x) || !is_numeric($wm_img_start_y)) {
            list($wm_img_start_x, $wm_img_start_y) = $this->calculateWatermarkImageStartXY(
                $wm_img_start_x,
                $wm_img_start_y,
                imagesx($this->Gd->destination_image_object),
                imagesy($this->Gd->destination_image_object),
                $watermark_width,
                $watermark_height,
                $options
            );
        }

        return [$wm_img_start_x, $wm_img_start_y];
    }// normalizeStartPosition


    /**
     * Setup watermark image object.
     * 
     * @param string $wm_img_path Path to watermark image.
     * @return bool Return true on success, false on failed. Call to `status_msg` property to see the details on failure.
     */
    private function setupWatermarkImageObject($wm_img_path)
    {
        try {
            $imageFileData = $this->getImageFileData($wm_img_path);
            if (is_array($imageFileData)) {
                list($wm_width, $wm_height, $wm_type) = $imageFileData;
            } else {
                $wm_width = $wm_height = $wm_type = null;
            }
            unset($imageFileData);
        } catch (\Exception $ex) {
            $this->setErrorMessage($ex->getMessage(), $ex->getCode());
            return false;
        }

        if ($wm_height == null || $wm_width == null || $wm_type == null) {
            $Gds = $this->Gd->getStatic();
            $this->setErrorMessage('Watermark is not an image.', $Gds::RDIERROR_WMI_UNKNOWIMG);
            unset($Gds);
            return false;
        }

        if ($this->isResourceOrGDObject($this->Gd->watermark_image_object) && version_compare(PHP_VERSION, '8.0', '<')) {
            imagedestroy($this->Gd->watermark_image_object);
        }
        $this->Gd->watermark_image_object = null;

        $wmObject = $this->setupSourceFromFile($wm_img_path, $wm_type);
        if ($this->isResourceOrGDObject($wmObject)) {
            $this->Gd->watermark_image_object = $wmObject;
        } else {
            $Gds = $this->Gd->getStatic();
            $this->setErrorMessage('Unable to set watermark from this kind of image.', $Gds::RDIERROR_WMI_UNKNOWIMG);
            unset($Gds);
            return false;
        }
        unset($wmObject);

        $this->Gd->watermark_image_height = $wm_height;
        $this->Gd->watermark_image_width = $wm_width;
        $this->Gd->watermark_image_type = $wm_type;

        unset($wm_height, $wm_img_path, $wm_type, $wm_width);
        $this->setStatusSuccess();
        return true;
    }// setupWatermarkImageObject


}
