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


    public function applyImage($wm_img_start_x = 0, $wm_img_start_y = 0)
    {
        switch ($this->Gd->watermark_image_type) {
            case IMAGETYPE_GIF:
            case IMAGETYPE_JPEG:
                imagecopy($this->Gd->source_image_object, $this->Gd->watermark_image_object, $wm_img_start_x, $wm_img_start_y, 0, 0, $this->Gd->watermark_image_width, $this->Gd->watermark_image_height);
                break;
            case IMAGETYPE_PNG:
                if ($this->Gd->source_image_type === IMAGETYPE_GIF) {
                    // if source image is gif (which maybe transparent) and watermark image is png. so, this cannot just use imagecopy() function.
                    // see more at http://stackoverflow.com/questions/4437557/using-gd-in-php-how-can-i-make-a-transparent-png-watermark-on-png-and-gif-files
                    $this->applyWatermarkToGifImage(
                        $this->Gd->watermark_image_object,
                        $this->Gd->watermark_image_width, 
                        $this->Gd->watermark_image_height, 
                        $wm_img_start_x, $wm_img_start_y
                    );
                } else {
                    imagealphablending($this->Gd->source_image_object, true);// add this for transparent watermark thru image.
                    imagecopy($this->Gd->source_image_object, $this->Gd->watermark_image_object, $wm_img_start_x, $wm_img_start_y, 0, 0, $this->Gd->watermark_image_width, $this->Gd->watermark_image_height);
                }
                break;
            default:
                $Gds = $this->Gd->getStatic();
                $this->Gd->status = false;
                $this->Gd->statusCode = $Gds::RDIERROR_WMI_UNKNOWIMG;
                $this->Gd->status_msg = 'Unable to set watermark from this kind of image.';
                unset($Gds);
                return false;
        }

        if ($this->isResourceOrGDObject($this->Gd->watermark_image_object)) {
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

        // find text width and height
        // @link http://stackoverflow.com/questions/11696920/calculating-text-width-with-php-gd Original source code.
        $type_space = imagettfbbox($wm_txt_font_size, 0, $wm_txt_font_path, $wm_txt_text);
        $wm_txt_height = abs($type_space[5] - $type_space[1]) + $this->Gd->wmTextBoundingBoxYPadding;
        $wm_txt_width = abs($type_space[4] - $type_space[0]) + 5;// +5 for add bounding box space to the right. so, it don't get cut before character end and for the same space as Imagick.
        unset($type_space);

        // if start x or y is number, convert to integer value
        if (is_numeric($wm_txt_start_x)) {
            $wm_txt_start_x = intval($wm_txt_start_x);
        }
        if (is_numeric($wm_txt_start_y)) {
            $wm_txt_start_y = intval($wm_txt_start_y);
        }

        // if start x or y is NOT number, find the real position of start x or y from word left, center, right, top, middle, bottom
        if (!is_numeric($wm_txt_start_x) || !is_numeric($wm_txt_start_y)) {
            if (!is_numeric($wm_txt_start_x)) {
                switch (strtolower($wm_txt_start_x)) {
                    case 'center':
                        $image_width = imagesx($this->Gd->source_image_object);
                        $watermark_width = $wm_txt_width;

                        $wm_txt_start_x = $this->calculateStartXOfCenter($watermark_width, $image_width);

                        unset($image_width, $watermark_width);
                        break;
                    case 'right':
                        $image_width = imagesx($this->Gd->source_image_object);
                        $wm_txt_start_x = intval(($image_width - $wm_txt_width) - 10);// add blank space to right.

                        unset($image_width);
                        break;
                    case 'left':
                    default:
                        $wm_txt_start_x = 10;// add blank space to left.
                        break;
                }
            }

            if (!is_numeric($wm_txt_start_y)) {
                switch (strtolower($wm_txt_start_y)) {
                    case 'middle':
                        $image_height = imagesy($this->Gd->source_image_object);
                        $watermark_height = $wm_txt_height;

                        $wm_txt_start_y = $this->calculateStartXOfCenter($watermark_height, $image_height);

                        unset($image_height, $watermark_height);
                        break;
                    case 'bottom':
                        $image_height = imagesy($this->Gd->source_image_object);
                        $wm_txt_start_y = intval($image_height - ($wm_txt_height + 10));// add blank space to bottom.
                        unset($image_height);
                        break;
                    case 'top':
                    default:
                        $wm_txt_start_y = 10;// add blank space to top.
                        break;
                }
            }
        }// endif; not number for start x or y

        // begins watermark text --------------------------------------------------------------------------------------------
        // create watermark text canvas
        $wm_txt_object = imagecreatetruecolor($wm_txt_width, $wm_txt_height);
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
        // write text
        // y coords below must -`emTextBottomPadding` to allow something like p, g show full size
        imagettftext(
            $wm_txt_object, 
            $wm_txt_font_size, 
            0, 
            0, 
            ($wm_txt_height - $this->Gd->wmTextBottomPadding), 
            $$wm_txt_font_color, 
            $wm_txt_font_path, 
            $wm_txt_text
        );

        // copy text to image
        switch ($this->Gd->source_image_type) {
            case IMAGETYPE_GIF:
                $this->applyWatermarkToGifImage(
                    $wm_txt_object, 
                    $wm_txt_width, 
                    $wm_txt_height, 
                    $wm_txt_start_x, 
                    $wm_txt_start_y
                );
                break;
            case IMAGETYPE_PNG:
            case IMAGETYPE_WEBP:
                imagealphablending($this->Gd->source_image_object, true);
            case IMAGETYPE_JPEG:
            default:
                imagecopy($this->Gd->source_image_object, $wm_txt_object, $wm_txt_start_x, $wm_txt_start_y, 0, 0, $wm_txt_width, $wm_txt_height);
                break;
        }
        // end watermark text -----------------------------------------------------------------------------------------------

        imagedestroy($wm_txt_object);
        unset($black, $blue, $colorDebugBg, $cyan, $fillWmBg, $green, $magenta, $red, $transwhite, $transwhitetext, $white, $wm_txt_height, $wm_txt_width, $yellow);

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
     * Apply watermark (text or image) object to GIF image object.
     * 
     * The `source_image_object` property must contain GIF image object.
     * 
     * @param resource|object $wmObject The watermark object, can be image or text.
     * @param int $wmWidth Watermark width
     * @param int $wmHeight Watermark height
     * @param int $wmStartX Watermark start X position.
     * @param int $wmStartY Watermark start Y position.
     */
    private function applyWatermarkToGifImage($wmObject, $wmWidth, $wmHeight, $wmStartX, $wmStartY)
    {
        // create temp canvas.
        $tempCanvas = imagecreatetruecolor($wmWidth, $wmHeight);

        // set temp canvas to be transparent. ---------
        imagesavealpha($tempCanvas, true);
        $TCColorForTransparent = imagecolorallocatealpha($tempCanvas, 255, 0, 255, 127);// look like pink or magenta.
        imagefill($tempCanvas, 0, 0, $TCColorForTransparent);
        imagecolortransparent($tempCanvas, $TCColorForTransparent);
        unset($TCColorForTransparent);
        // end set temp canvas transparent. -----------

        // copy part of source image to temp canvas where the size is same as watermark canvas.
        imagecopy($tempCanvas, $this->Gd->source_image_object, 0, 0, $wmStartX, $wmStartY, $wmWidth, $wmHeight);
        // copy the whole watermark canvas to temp canvas.
        imagecopy($tempCanvas, $wmObject, 0, 0, 0, 0, $wmWidth, $wmHeight);
        // copy merge temp canvas to image object.
        imagecopymerge($this->Gd->source_image_object, $tempCanvas, $wmStartX, $wmStartY, 0, 0, $wmWidth, $wmHeight, 100);
        // destroy temp canvas.
        imagedestroy($tempCanvas);
        unset($tempCanvas);
    }// applyWatermarkToGifImage


    /**
     * Setup watermark image object.
     * 
     * @param string $wm_img_path Path to watermark image.
     * @return bool Return true on success, false on failed. Call to `status_msg` property to see the details on failure.
     */
    public function setupWatermarkImageObject($wm_img_path)
    {
        if (!is_file($wm_img_path)) {
            $Gds = $this->Gd->getStatic();
            $this->Gd->status = false;
            $this->Gd->statusCode = $Gds::RDIERROR_WMI_NOTEXISTS;
            $this->Gd->status_msg = 'Watermark image was not found.';
            unset($Gds);
            return false;
        }

        try {
            list($wm_width, $wm_height, $wm_type) = $this->getImageFileData($wm_img_path);
        } catch (\Exception $ex) {
            $this->Gd->status = false;
            $this->Gd->statusCode = $ex->getCode();
            $this->Gd->status_msg = $ex->getMessage();
            return false;
        }

        if ($wm_height == null || $wm_width == null || $wm_type == null) {
            $Gds = $this->Gd->getStatic();
            $this->Gd->status = false;
            $this->Gd->statusCode = $Gds::RDIERROR_WMI_UNKNOWIMG;
            $this->Gd->status_msg = 'Watermark is not an image.';
            unset($Gds);
            return false;
        }

        if ($this->isResourceOrGDObject($this->Gd->watermark_image_object)) {
            imagedestroy($this->Gd->watermark_image_object);
        }

        switch ($wm_type) {
            case IMAGETYPE_GIF:
                $this->Gd->watermark_image_object = imagecreatefromgif($wm_img_path);
                // add alpha to support transparency gif
                imagesavealpha($this->Gd->watermark_image_object, true);
                break;
            case IMAGETYPE_JPEG:
                $this->Gd->watermark_image_object = imagecreatefromjpeg($wm_img_path);
                break;
            case IMAGETYPE_PNG:
                $this->Gd->watermark_image_object = imagecreatefrompng($wm_img_path);
                // add alpha, alpha blending to support transparency png
                imagealphablending($this->Gd->watermark_image_object, false);
                imagesavealpha($this->Gd->watermark_image_object, true);
                break;
            default:
                $Gds = $this->Gd->getStatic();
                $this->Gd->status = false;
                $this->Gd->statusCode = $Gds::RDIERROR_WMI_UNKNOWIMG;
                $this->Gd->status_msg = 'Unable to set watermark from this kind of image.';
                unset($Gds);
                return false;
        }

        $this->Gd->watermark_image_height = $wm_height;
        $this->Gd->watermark_image_width = $wm_width;
        $this->Gd->watermark_image_type = $wm_type;

        unset($wm_height, $wm_img_path, $wm_type, $wm_width);
        $this->Gd->status = true;
        $this->Gd->statusCode = null;
        $this->Gd->status_msg = null;
        return true;
    }// setupWatermarkImageObject


}
