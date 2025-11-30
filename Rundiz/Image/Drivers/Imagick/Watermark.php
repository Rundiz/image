<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Drivers\Imagick;


/**
 * Watermark image and text.
 * 
 * @since 3.1.0
 */
class Watermark extends \Rundiz\Image\Drivers\AbstractImagickCommand
{


    use \Rundiz\Image\Traits\CalculationTrait;


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

        if ($this->ImagickD->source_image_frames > 1) {
            $this->ImagickD->Imagick = $this->ImagickD->Imagick->coalesceImages();
            if (is_object($this->ImagickD->Imagick)) {
                $i = 1;
                foreach ($this->ImagickD->Imagick as $Frame) {
                    $Frame->compositeImage($this->ImagickD->ImagickWatermark, \Imagick::COMPOSITE_DEFAULT, $wm_img_start_x, $wm_img_start_y);
                    if ($i == 1) {
                        $this->ImagickD->ImagickFirstFrame = $Frame->getImage();
                    }
                    $i++;
                }// endforeach;
                unset($Frame, $i);
            }
        } else {
            $this->ImagickD->Imagick->compositeImage($this->ImagickD->ImagickWatermark, \Imagick::COMPOSITE_DEFAULT, $wm_img_start_x, $wm_img_start_y);
            $this->ImagickD->ImagickFirstFrame = null;
        }// endif;

        if (is_object($this->ImagickD->ImagickWatermark)) {
            $this->ImagickD->ImagickWatermark->clear();
        }

        $this->ImagickD->destination_image_height = $this->ImagickD->Imagick->getImageHeight();
        $this->ImagickD->destination_image_width = $this->ImagickD->Imagick->getImageWidth();

        $this->ImagickD->source_image_height = $this->ImagickD->destination_image_height;
        $this->ImagickD->source_image_width = $this->ImagickD->destination_image_width;

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
        $ImagickDraw = new \ImagickDraw();
        $ImagickDraw->setFont($wm_txt_font_path);
        // note: Imagick font size in php 5.4 is always smaller, it is about 10. Or maybe it's depend on each font.
        $ImagickDraw->setFontSize($wm_txt_font_size);
        // don't use `setGravity()` because it is for position text on source image. (top left, bottom right, etc.)
        // use custom position Y that is +baseline +watermark text height instead, this way it will be the same result as GD.
        // set new resolution (DPI) for font due to it is smaller than GD if it was not set.
        $ImagickDraw->setresolution(96, 96);
        $type_space = $this->ImagickD->Imagick->queryFontMetrics($ImagickDraw, $wm_txt_text, false);
        if (is_array($type_space) && array_key_exists('textWidth', $type_space) && array_key_exists('textHeight', $type_space)) {
            $wm_txt_height = $type_space['textHeight'] + $this->ImagickD->wmTextBoundingBoxYPadding;
            $wm_txt_width = $type_space['textWidth'];
        }
        unset($type_space);
        // set baseline.
        if (!is_numeric($this->ImagickD->imagickWatermarkTextBaseline)) {
            $baseline = 0;
        } else {
            $baseline = intval($this->ImagickD->imagickWatermarkTextBaseline);
        }
        $baseline = ($baseline - $this->ImagickD->wmTextBottomPadding);

        list($wm_txt_start_x, $wm_txt_start_y) = $this->normalizeStartPosition($wm_txt_start_x, $wm_txt_start_y, $wm_txt_width, $wm_txt_height, $options);

        // begins watermark text --------------------------------------------------------------------------------------------
        $this->createWatermarkTextObject($ImagickDraw, $wm_txt_start_x, $wm_txt_start_y, $wm_txt_width, $wm_txt_height, $wm_txt_font_color, $wm_txt_font_alpha, $options);

        // write text on image
        if ($this->ImagickD->source_image_frames > 1) {
            // if source image is animated gif
            $this->ImagickD->Imagick = $this->ImagickD->Imagick->coalesceImages();
            if (is_object($this->ImagickD->Imagick)) {
                $i = 1;
                foreach ($this->ImagickD->Imagick as $Frame) {
                    $Frame->annotateImage($ImagickDraw, $wm_txt_start_x, (($wm_txt_start_y + $baseline) + $wm_txt_height), 0, $wm_txt_text);
                    if ($i == 1) {
                        $this->ImagickD->ImagickFirstFrame = $Frame->getImage();
                    }
                    $i++;
                }
                unset($Frame, $i);
            }
        } else {
            $this->ImagickD->Imagick->annotateImage($ImagickDraw, $wm_txt_start_x, (($wm_txt_start_y + $baseline) + $wm_txt_height), 0, $wm_txt_text);
            $this->ImagickD->ImagickFirstFrame = null;
        }
        // end watermark text -----------------------------------------------------------------------------------------------

        $ImagickDraw->clear();
        unset($baseline, $ImagickDraw, $wm_txt_height, $wm_txt_width);

        $this->ImagickD->destination_image_height = $this->ImagickD->Imagick->getImageHeight();
        $this->ImagickD->destination_image_width = $this->ImagickD->Imagick->getImageWidth();

        $this->ImagickD->source_image_height = $this->ImagickD->destination_image_height;
        $this->ImagickD->source_image_width = $this->ImagickD->destination_image_width;

        return true;
    }// applyText


    /**
     * Create watermark text object.
     * 
     * @see Rundiz\Image\ImageInterface::watermarkText() for more details.
     * @since 3.1.4
     * @param \ImagickDraw $ImagickDraw
     * @param int $wm_txt_start_x
     * @param int $wm_txt_start_y
     * @param int $wm_txt_width
     * @param int $wm_txt_height
     * @param string $wm_txt_font_color
     * @param int $wm_txt_font_alpha
     * @param array $options
     */
    private function createWatermarkTextObject(
        $ImagickDraw,
        $wm_txt_start_x, 
        $wm_txt_start_y, 
        $wm_txt_width, 
        $wm_txt_height, 
        $wm_txt_font_color, 
        $wm_txt_font_alpha, 
        $options = []
    ) {
        // set color
        $black = new \ImagickPixel('black');
        $white = new \ImagickPixel('white');
        $red = new \ImagickPixel('rgb(255, 0, 0)');
        $green = new \ImagickPixel('rgb(0, 255, 0)');
        $blue = new \ImagickPixel('rgb(0, 0, 255)');
        $yellow = new \ImagickPixel('rgb(255, 255, 0)');
        $cyan = new \ImagickPixel('rgb(0, 255, 255)');
        $magenta = new \ImagickPixel('rgb(255, 0, 255)');
        $colorDebugBg = new \ImagickPixel('rgba(0, 0, 255, 0.3)');
        $transwhite = new \ImagickPixel('rgba(255, 255, 255, 0)');// set color transparent white
        $transwhitetext = new \ImagickPixel('rgba(255, 255, 255, '.$this->convertAlpha127ToRgba($wm_txt_font_alpha).')');

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
        $ImagickDraw->setFillColor($fillWmBg);
        $ImagickDraw->rectangle($wm_txt_start_x, $wm_txt_start_y, ($wm_txt_start_x + $wm_txt_width), ($wm_txt_start_y + $wm_txt_height));
        $this->ImagickD->Imagick->drawImage($ImagickDraw);
        // fill font color
        $ImagickDraw->setFillColor($$wm_txt_font_color);

        // clear
        $black->clear();
        $blue->clear();
        $colorDebugBg->clear();
        $cyan->clear();
        $green->clear();
        $magenta->clear();
        $red->clear();
        $transwhite->clear();
        $transwhitetext->clear();
        $white->clear();
        $yellow->clear();

        // unset
        unset($black, $blue, $colorDebugBg, $cyan, $green, $magenta, $red, $transwhite, $transwhitetext, $white, $yellow);
    }// drawWatermarkTextOnCanvas


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
            $watermark_width = $this->ImagickD->watermark_image_width;
        }
        if (is_null($watermark_height)) {
            $watermark_height = $this->ImagickD->watermark_image_height;
        }

        // if start x or y is NOT number, calculate the real position of start x or y from word left, center, right, top, middle, bottom
        if (!is_numeric($wm_img_start_x) || !is_numeric($wm_img_start_y)) {
            list($wm_img_start_x, $wm_img_start_y) = $this->calculateWatermarkImageStartXY(
                $wm_img_start_x,
                $wm_img_start_y,
                $this->ImagickD->Imagick->getImageWidth(),
                $this->ImagickD->Imagick->getImageHeight(),
                $watermark_width,
                $watermark_height,
                $options
            );
        }

        return [$wm_img_start_x, $wm_img_start_y];
    }// normalizeStartPosition


    /**
     * Setup watermark image object.
     * After calling this the ImagickWatermark will get new ImageMagick object if it does not set before.
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

        $wm_img_path = realpath($wm_img_path);

        if (is_object($this->ImagickD->ImagickWatermark)) {
            $this->ImagickD->ImagickWatermark->clear();
            $this->ImagickD->ImagickWatermark = null;
        }

        try {
            $this->ImagickD->ImagickWatermark = new \Imagick($wm_img_path);
        } catch (\Exception $ex) {
            $this->setErrorMessage($ex->getMessage(), $ex->getCode());
            return false;
        }

        if (!is_object($this->ImagickD->ImagickWatermark)) {
            $Ims = $this->ImagickD->getStatic();
            $this->setErrorMessage('Unable to set watermark from this kind of image.', $Ims::RDIERROR_WMI_UNKNOWIMG);
            unset($Ims);
            return false;
        }

        $this->ImagickD->watermark_image_height = $wm_height;
        $this->ImagickD->watermark_image_width = $wm_width;
        $this->ImagickD->watermark_image_type = $wm_type;

        unset($wm_height, $wm_img_path, $wm_type, $wm_width);
        $this->setStatusSuccess();
        return true;
    }// setupWatermarkImageObject


}
