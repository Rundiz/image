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


    public function applyImage($wm_img_start_x = 0, $wm_img_start_y = 0)
    {
        switch ($this->ImagickD->watermark_image_type) {
            case IMAGETYPE_GIF:
            case IMAGETYPE_JPEG:
            case IMAGETYPE_PNG:
                if ($this->ImagickD->source_image_frames > 1) {
                    // if source image is animated gif
                    $this->ImagickD->Imagick = $this->ImagickD->Imagick->coalesceImages();
                    if (is_object($this->ImagickD->Imagick)) {
                        $i = 1;
                        foreach ($this->ImagickD->Imagick as $Frame) {
                            $Frame->compositeImage($this->ImagickD->ImagickWatermark, \Imagick::COMPOSITE_DEFAULT, $wm_img_start_x, $wm_img_start_y);
                            $Frame->setImagePage(0, 0, 0, 0);
                            if ($i == 1) {
                                $this->ImagickD->ImagickFirstFrame = $Frame->getImage();
                            }
                            $i++;
                        }
                        unset($Frame, $i);
                    }
                } else {
                    $this->ImagickD->Imagick->compositeImage($this->ImagickD->ImagickWatermark, \Imagick::COMPOSITE_DEFAULT, $wm_img_start_x, $wm_img_start_y);
                    if ($this->ImagickD->source_image_type === IMAGETYPE_GIF) {
                        // if source image is gif, set image page to prevent sizing error.
                        $this->ImagickD->Imagick->setImagePage(0, 0, 0, 0);
                    }
                    $this->ImagickD->ImagickFirstFrame = null;
                }
                break;
            default:
                $Ims = $this->ImagickD->getStatic();
                $this->ImagickD->status = false;
                $this->ImagickD->statusCode = $Ims::RDIERROR_WMI_UNKNOWIMG;
                $this->ImagickD->status_msg = 'Unable to set watermark from this kind of image.';
                unset($Ims);
                return false;
        }

        if (is_object($this->ImagickD->ImagickWatermark)) {
            $this->ImagickD->ImagickWatermark->clear();
        }

        $this->ImagickD->destination_image_height = $this->ImagickD->Imagick->getImageHeight();
        $this->ImagickD->destination_image_width = $this->ImagickD->Imagick->getImageWidth();

        $this->ImagickD->source_image_height = $this->ImagickD->destination_image_height;
        $this->ImagickD->source_image_width = $this->ImagickD->destination_image_width;

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
        $ImagickDraw = new \ImagickDraw();
        $ImagickDraw->setFont($wm_txt_font_path);
        // note: Imagick font size in php 5.4 is smaller about 10.
        $ImagickDraw->setFontSize($wm_txt_font_size);
        // don't use `setGravity()` because it is for position text on source image. (top left, bottom right, etc.)
        // use custom position Y that is +baseline +watermark text height instead, this way it is the same as GD.
        // set new resolution for font due to it is smaller than GD if it was not set.
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
                        $image_width = $this->ImagickD->Imagick->getImageWidth();
                        $watermark_width = $wm_txt_width;

                        $wm_txt_start_x = $this->calculateStartXOfCenter($watermark_width, $image_width);

                        unset($image_width, $watermark_width);
                        break;
                    case 'right':
                        $image_width = $this->ImagickD->Imagick->getImageWidth();
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
                        $image_height = $this->ImagickD->Imagick->getImageHeight();
                        $watermark_height = $wm_txt_height;

                        $wm_txt_start_y = $this->calculateStartXOfCenter($watermark_height, $image_height);

                        unset($image_height, $watermark_height);
                        break;
                    case 'bottom':
                        $image_height = $this->ImagickD->Imagick->getImageHeight();
                        $wm_txt_start_y = intval($image_height - (($wm_txt_height + 10) - $baseline));// add blank space to bottom.
                        unset($image_height);
                        break;
                    case 'top':
                    default:
                        $wm_txt_start_y = 10;// add blank space to top.
                        break;
                }
            }
        }// endif ; not number for start x or y

        // begins watermark text --------------------------------------------------------------------------------------------
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

        // write text on image
        if ($this->ImagickD->source_image_frames > 1) {
            // if source image is animated gif
            $this->ImagickD->Imagick = $this->ImagickD->Imagick->coalesceImages();
            if (is_object($this->ImagickD->Imagick)) {
                $i = 1;
                foreach ($this->ImagickD->Imagick as $Frame) {
                    $Frame->annotateImage($ImagickDraw, $wm_txt_start_x, (($wm_txt_start_y + $baseline) + $wm_txt_height), 0, $wm_txt_text);
                    $Frame->setImagePage(0, 0, 0, 0);
                    if ($i == 1) {
                        $this->ImagickD->ImagickFirstFrame = $Frame->getImage();
                    }
                    $i++;
                }
                unset($Frame, $i);
            }
        } else {
            $this->ImagickD->Imagick->annotateImage($ImagickDraw, $wm_txt_start_x, (($wm_txt_start_y + $baseline) + $wm_txt_height), 0, $wm_txt_text);
            if ($this->ImagickD->source_image_type === IMAGETYPE_GIF) {
                // if source image is gif, set image page to prevent sizing error.
                $this->ImagickD->Imagick->setImagePage(0, 0, 0, 0);
            }
            $this->ImagickD->ImagickFirstFrame = null;
        }
        // end watermark text -----------------------------------------------------------------------------------------------

        $ImagickDraw->clear();
        $black->destroy();
        $transwhite->destroy();
        $transwhitetext->destroy();
        $white->destroy();
        unset($baseline, $black, $blue, $colorDebugBg, $cyan, $fillWmBg, $green, $magenta, $ImagickDraw, $red, $transwhite, $transwhitetext, $white, $wm_txt_height, $wm_txt_width, $yellow);

        $this->ImagickD->destination_image_height = $this->ImagickD->Imagick->getImageHeight();
        $this->ImagickD->destination_image_width = $this->ImagickD->Imagick->getImageWidth();

        $this->ImagickD->source_image_height = $this->ImagickD->destination_image_height;
        $this->ImagickD->source_image_width = $this->ImagickD->destination_image_width;

        return true;
    }// applyText


    /**
     * Setup watermark image object.
     * After calling this the ImagickWatermark will get new Image Magick object if it does not set before.
     * 
     * @param string $wm_img_path Path to watermark image.
     * @return bool Return true on success, false on failed. Call to `status_msg` property to see the details on failure.
     */
    public function setupWatermarkImageObject($wm_img_path)
    {
        if (!is_file($wm_img_path)) {
            $Ims = $this->ImagickD->getStatic();
            $this->ImagickD->status = false;
            $this->ImagickD->statusCode = $Ims::RDIERROR_WMI_NOTEXISTS;
            $this->ImagickD->status_msg = 'Watermark image was not found.';
            unset($Ims);
            return false;
        }
        $wm_img_path = realpath($wm_img_path);

        list($wm_width, $wm_height, $wm_type) = $this->getImageFileData($wm_img_path);

        if ($wm_height == null || $wm_width == null || $wm_type == null) {
            $Ims = $this->ImagickD->getStatic();
            $this->ImagickD->status = false;
            $this->ImagickD->statusCode = $Ims::RDIERROR_WMI_UNKNOWIMG;
            $this->ImagickD->status_msg = 'Watermark is not an image.';
            unset($Ims);
            return false;
        }

        if (is_object($this->ImagickD->ImagickWatermark)) {
            $this->ImagickD->ImagickWatermark->clear();
            $this->ImagickD->ImagickWatermark = null;
        }

        if ($this->ImagickD->ImagickWatermark == null || !is_object($this->ImagickD->ImagickWatermark)) {
            $this->ImagickD->ImagickWatermark = new \Imagick($wm_img_path);

            if ($this->ImagickD->ImagickWatermark == null || !is_object($this->ImagickD->ImagickWatermark)) {
                $Ims = $this->ImagickD->getStatic();
                $this->ImagickD->status = false;
                $this->ImagickD->statusCode = $Ims::RDIERROR_WMI_UNKNOWIMG;
                $this->ImagickD->status_msg = 'Unable to set watermark from this kind of image.';
                unset($Ims);
                return false;
            }
        }

        $this->ImagickD->watermark_image_height = $wm_height;
        $this->ImagickD->watermark_image_width = $wm_width;
        $this->ImagickD->watermark_image_type = $wm_type;

        unset($wm_height, $wm_img_path, $wm_type, $wm_width);
        $this->ImagickD->status = true;
        $this->ImagickD->statusCode = null;
        $this->ImagickD->status_msg = null;
        return true;
    }// setupWatermarkImageObject


}
