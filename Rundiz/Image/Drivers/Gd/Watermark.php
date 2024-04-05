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
     *              `padding` (int) Padding around watermark object. Use with left, right, bottom, top but not middle, center. See `\Rundiz\Image\Traits\CalculationTrait::calculateWatermarkImageStartXY()`.<br>
     * @return bool Return `true` on success, `false` on failure. Call to `status_msg` property to see the details on failure.
     */
    public function applyImage($wm_img_path, $wm_img_start_x = 0, $wm_img_start_y = 0, array $options = [])
    {
        if (false === $this->setupWatermarkImageObject($wm_img_path)) {
            return false;
        }

        list($wm_img_start_x, $wm_img_start_y) = $this->normalizeStartPosition($wm_img_start_x, $wm_img_start_y, null, null, $options);

        // copy watermark image on to source image (in this case, it is destination image object).
        imagecopy($this->Gd->destination_image_object, $this->Gd->watermark_image_object, $wm_img_start_x, $wm_img_start_y, 0, 0, $this->Gd->watermark_image_width, $this->Gd->watermark_image_height);

        if ($this->isResourceOrGDObject($this->Gd->watermark_image_object)) {
            // if there is watermark image object.
            imagedestroy($this->Gd->watermark_image_object);
        }

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
     * Apply watermark text to source image.
     * 
     * @see Rundiz\Image\ImageInterface::watermarkText() for more details.
     * @param string $wm_txt_text Watermark text
     * @param string $wm_txt_font_path 'True Type Font' path
     * @param int|string $wm_txt_start_x Position to begin in x axis. The value is integer or 'left', 'center', 'right'.
     * @param int|string $wm_txt_start_y Position to begin in x axis. The value is integer or 'top', 'middle', 'bottom'.
     * @param int $wm_txt_font_size Font size
     * @param string $wm_txt_font_color Font color. ('black', 'white', 'red', 'green', 'blue', 'yellow', 'cyan', 'magenta', 'transwhitetext')
     * @param int $wm_txt_font_alpha Text transparency value. (0-127)
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
        $wm_txt_font_color = 'transwhitetext', 
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

        imagedestroy($wm_txt_object);
        unset($wm_txt_height, $wm_txt_width);

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

        // set color
        $black = imagecolorallocate($wm_txt_object, 0, 0, 0);
        $white = imagecolorallocate($wm_txt_object, 255, 255, 255);
        $red = imagecolorallocate($wm_txt_object, 255, 0, 0);
        $green = imagecolorallocate($wm_txt_object, 0, 255, 0);
        $blue = imagecolorallocate($wm_txt_object, 0, 0, 255);
        $yellow = imagecolorallocate($wm_txt_object, 255, 255, 0);
        $cyan = imagecolorallocate($wm_txt_object, 0, 255, 255);
        $magenta = imagecolorallocate($wm_txt_object, 255, 0, 255);
        $colorDebugBg = imagecolorallocatealpha($wm_txt_object, 0, 0, 255, 85);
        $transwhite = imagecolorallocatealpha($wm_txt_object, 255, 255, 255, 127);// set color transparent white
        $transwhitetext = imagecolorallocatealpha($wm_txt_object, 255, 255, 255, $wm_txt_font_alpha);

        if (!isset($$wm_txt_font_color)) {
            $wm_txt_font_color = 'transwhitetext';
        }
        $fillWmBg = $transwhite;

        if (isset($options['fillBackground']) && $options['fillBackground'] === true) {
            if (isset($options['backgroundColor'])) {
                $colorName = $options['backgroundColor'];
                if (strtolower($colorName) === 'colordebugbg' || strtolower($colorName) === 'debug') {
                    $colorName = 'colorDebugBg';
                }
                if (isset($$colorName)) {
                    $fillWmBg = $$colorName;
                }
                unset($colorName);
            }
        }

        // fill background color
        imagefill($wm_txt_object, 0, 0, $fillWmBg);

        unset($fillWmBg);

        $textColor = $$wm_txt_font_color;
        unset($black, $blue, $colorDebugBg, $cyan, $green, $magenta, $red, $transwhite, $transwhitetext, $white, $yellow);

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
            list($wm_width, $wm_height, $wm_type) = $this->getImageFileData($wm_img_path);
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

        if ($this->isResourceOrGDObject($this->Gd->watermark_image_object)) {
            imagedestroy($this->Gd->watermark_image_object);
        }

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
