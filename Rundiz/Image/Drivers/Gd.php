<?php
/**
 * PHP Image manipulation class.
 * 
 * @package Image
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 */


namespace Rundiz\Image\Drivers;

use Rundiz\Image\AbstractImage;

/**
 * GD driver for image manipulation.
 *
 * @since 3.0
 * @property-read mixed $destination_image_object
 * @property-read mixed $source_image_object
 * @property-read mixed $watermark_image_object
 */
class Gd extends AbstractImage
{


    use Traits\GdTrait;


    /**
     * @var mixed Image resource identifier
     */
    protected $destination_image_object = null;
    /**
     * @var mixed Image resource identifier
     */
    protected $source_image_object = null;

    /**
     * @var mixed Image resource identifier for watermark image.
     */
    protected $watermark_image_object = null;


    /**
     * {@inheritDoc}
     */
    public function __construct($source_image_path)
    {
        return parent::__construct($source_image_path);
    }// __construct


    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        if ($this->isResourceOrGDObject($this->destination_image_object)) {
            imagedestroy($this->destination_image_object);
        }

        if ($this->isResourceOrGDObject($this->source_image_object)) {
            imagedestroy($this->source_image_object);
        }

        if ($this->isResourceOrGDObject($this->watermark_image_object)) {
            imagedestroy($this->watermark_image_object);
        }

        $this->destination_image_object = null;
        $this->source_image_object = null;
        $this->watermark_image_object = null;

        parent::clear();
        $this->buildSourceImageData($this->source_image_path);

        return true;
    }// clear


    /**
     * {@inheritDoc}
     */
    public function crop($width, $height, $start_x = '0', $start_y = '0', $fill = 'transparent')
    {
        if (false === $this->isClassSetup()) {
            return false;
        }

        // setup source and destination image objects
        if (false === $this->setupSourceImageObject()) {
            return false;
        }
        if (false === $this->setupDestinationImageObjectWithSize($width, $height)) {
            return false;
        }

        // check previous step contain errors?
        if ($this->isPreviousError() === true) {
            return false;
        }

        // convert width and height to integer
        list($width, $height) = $this->normalizeWidthHeight($width, $height);

        $Crop = new Gd\Crop($this);
        $result = $Crop->execute($width, $height, $start_x, $start_y, $fill);
        unset($Crop);
        if (false === $result) {
            return false;
        }
        unset($result);

        $this->last_modified_image_height = $height;
        $this->last_modified_image_width = $width;
        $this->destination_image_height = $height;
        $this->destination_image_width = $width;

        return true;
    }// crop


    /**
     * Get this class as static call.
     * 
     * @return static
     */
    public function getStatic()
    {
        return new static($this->source_image_path);
    }// getStatic


    /**
     * {@inheritDoc}
     */
    public function resizeNoRatio($width, $height)
    {
        if (false === $this->isClassSetup()) {
            return false;
        }

        // setup source and destination image objects
        if (false === $this->setupSourceImageObject()) {
            return false;
        }
        if (false === $this->setupDestinationImageObjectWithSize($width, $height)) {
            return false;
        }

        // check previous step contain errors?
        if ($this->isPreviousError() === true) {
            return false;
        }

        // convert width and height to integer
        list($width, $height) = $this->normalizeWidthHeight($width, $height);

        $Resize = new Gd\Resize($this);
        $result = $Resize->execute($width, $height);
        unset($Resize);
        if (false === $result) {
            return false;
        }
        unset($result);

        // set new value to properties
        $this->last_modified_image_height = $height;
        $this->last_modified_image_width = $width;
        $this->destination_image_height = $height;
        $this->destination_image_width = $width;

        return true;
    }// resizeNoRatio


    /**
     * {@inheritDoc}
     */
    public function rotate($degree = 90)
    {
        if (false === $this->isClassSetup()) {
            return false;
        }

        // setup source image object
        if (false === $this->setupSourceImageObject()) {
            return false;
        }

        // check previous step contain errors?
        if ($this->isPreviousError() === true) {
            return false;
        }

        $Rotate = new Gd\Rotate($this);
        $result = $Rotate->execute($degree);
        unset($Rotate);
        if (false === $result) {
            return false;
        }
        unset($result);

        if (!$this->isPreviousError()) {
            $this->setStatusSuccess();
        }
        return true;
    }// rotate


    /**
     * {@inheritDoc}
     */
    public function save($file_name)
    {
        if (false === $this->isClassSetup()) {
            return false;
        }

        // in case that it was called new Gd object and then save without any modification.
        if ($this->destination_image_object == null && $this->source_image_object == null) {
            $this->resizeNoRatio($this->source_image_width, $this->source_image_height);
        }

        // check previous step contain errors?
        if ($this->isPreviousError() === true) {
            return false;
        }

        $Save = new Gd\Save($this);
        return $Save->execute($file_name);
    }// save


    /**
     * Setup destination image object that must have size in width and height for use with resize, crop.
     * 
     * @param int $width Destination image object width.
     * @param int $height Destination image object height.
     * @return bool Return true on success, false on failed.
     */
    private function setupDestinationImageObjectWithSize($width, $height)
    {
        if (false === $this->isClassSetup()) {
            return false;
        }

        if ($this->isPreviousError() === true) {
            return false;
        }

        if (!$this->isResourceOrGDObject($this->destination_image_object)) {
            $this->destination_image_object = imagecreatetruecolor($width, $height);
        }

        // come to this means destination image object is already set.
        $this->setStatusSuccess();
        return true;
    }// setupDestinationImageObjectWithSize


    /**
     * Setup source image object.
     * After calling this the source_image_object will get new image resource by chaining from previous destination or from image file.
     * 
     * @return bool Return true on success, false on failed.
     */
    private function setupSourceImageObject()
    {
        if (false === $this->isClassSetup()) {
            return false;
        }

        if ($this->isPreviousError() === true) {
            return false;
        }

        if (!$this->isResourceOrGDObject($this->source_image_object)) {
            if ($this->isResourceOrGDObject($this->destination_image_object)) {
                $this->source_image_object = $this->destination_image_object;
                $this->destination_image_object = null;

                $this->setStatusSuccess();
                return true;
            } else {
                if ($this->source_image_type === IMAGETYPE_GIF) {
                    // gif
                    $this->source_image_object = imagecreatefromgif($this->source_image_path);
                    // add alpha to support transparency gif
                    imagesavealpha($this->source_image_object, true);
                } elseif ($this->source_image_type === IMAGETYPE_JPEG) {
                    // jpg
                    $this->source_image_object = imagecreatefromjpeg($this->source_image_path);
                } elseif ($this->source_image_type === IMAGETYPE_PNG) {
                    // png
                    $this->source_image_object = imagecreatefrompng($this->source_image_path);
                    // add alpha, alpha blending to support transparency png
                    imagealphablending($this->source_image_object, false);
                    imagesavealpha($this->source_image_object, true);
                } elseif ($this->source_image_type === IMAGETYPE_WEBP) {
                    // webp
                    $WebP = new \Rundiz\Image\Extensions\WebP($this->source_image_path);
                    if ($WebP->isGDSupported()) {
                        // if GD supported this WEBP. If not then it will be in `source_image_object` to be `null` or empty.
                        $this->source_image_object = imagecreatefromwebp($this->source_image_path);
                        // add alpha, alpha blending to support transparency webp
                        imagealphablending($this->source_image_object, false);
                        imagesavealpha($this->source_image_object, true);
                    }// endif; GD supported.
                    unset($WebP);
                }// endif;

                if ($this->source_image_object != null) {
                    $this->setStatusSuccess();
                    return true;
                } else {
                    $this->setErrorMessage('Unable to set source from this kind of image.', static::RDIERROR_SRC_UNKNOWN);
                    return false;
                }
            }
        }

        // come to this means source image object is already set.
        $this->setStatusSuccess();
        return true;
    }// setupSourceImageObject


    /**
     * {@inheritDoc}
     */
    public function show($file_ext = '')
    {
        if (false === $this->isClassSetup()) {
            return false;
        }

        // in case that it was called new Gd object and then save without any modification.
        if ($this->destination_image_object == null && $this->source_image_object == null) {
            $this->resizeNoRatio($this->source_image_width, $this->source_image_height);
        }

        // check previous step contain errors?
        if ($this->isPreviousError() === true) {
            return false;
        }

        $Show = new Gd\Show($this);
        return $Show->execute($file_ext);
    }// show


    /**
     * {@inheritDoc}
     */
    public function watermarkImage($wm_img_path, $wm_img_start_x = 0, $wm_img_start_y = 0, array $options = [])
    {
        if (false === $this->isClassSetup()) {
            return false;
        }

        // check watermark image path exists
        if (!is_file($wm_img_path)) {
            $this->setErrorMessage('Watermark image was not found.', static::RDIERROR_WMI_NOTEXISTS);
            return false;
        }

        // setup source image object
        if (false === $this->setupSourceImageObject()) {
            return false;
        }

        // check previous step contain errors?
        if ($this->isPreviousError() === true) {
            return false;
        }

        // if start x or y is number, convert to integer value
        if (is_numeric($wm_img_start_x)) {
            $wm_img_start_x = intval($wm_img_start_x);
        }
        if (is_numeric($wm_img_start_y)) {
            $wm_img_start_y = intval($wm_img_start_y);
        }

        // setup watermark object for use later.
        $Watermark = new Gd\Watermark($this);
        $Watermark->setupWatermarkImageObject($wm_img_path);
        unset($Watermark);

        // if start x or y is NOT number, find the real position of start x or y from word left, center, right, top, middle, bottom
        if (!is_numeric($wm_img_start_x) || !is_numeric($wm_img_start_y)) {
            if ($this->isPreviousError()) {
                return false;
            }

            list($wm_img_start_x, $wm_img_start_y) = $this->calculateWatermarkImageStartXY(
                $wm_img_start_x,
                $wm_img_start_y,
                imagesx($this->source_image_object),
                imagesy($this->source_image_object),
                $this->watermark_image_width,
                $this->watermark_image_height,
                $options
            );
        }

        return $this->watermarkImageProcess($wm_img_path, $wm_img_start_x, $wm_img_start_y);
    }// watermarkImage


    /**
     * Process watermark image to the main image.
     * 
     * @param string $wm_img_path Path to watermark image.
     * @param int $wm_img_start_x Position to begin in x axis. The value is integer or 'left', 'center', 'right'.
     * @param int $wm_img_start_y Position to begin in x axis. The value is integer or 'top', 'middle', 'bottom'.
     * @return bool Return true on success, false on failed.
     */
    private function watermarkImageProcess($wm_img_path, $wm_img_start_x = 0, $wm_img_start_y = 0)
    {
        if ($this->isPreviousError()) {
            return false;
        }

        $Watermark = new Gd\Watermark($this);
        $result = $Watermark->applyImage($wm_img_start_x, $wm_img_start_y);
        unset($Watermark);
        if (false === $result) {
            return false;
        }
        unset($result);

        $this->setStatusSuccess();
        return true;
    }// watermarkImageProcess


    /**
     * {@inheritDoc}
     */
    public function watermarkText(
        $wm_txt_text, 
        $wm_txt_font_path, 
        $wm_txt_start_x = 0, 
        $wm_txt_start_y = 0, 
        $wm_txt_font_size = 10, 
        $wm_txt_font_color = 'transwhitetext', 
        $wm_txt_font_alpha = 60,
        array $options = []
    ) {
        if (false === $this->isClassSetup()) {
            return false;
        }

        // setup source image object
        if (false === $this->setupSourceImageObject()) {
            return false;
        }

        // check previous step contain errors?
        if ($this->isPreviousError() === true) {
            return false;
        }

        if (!is_file($wm_txt_font_path)) {
            $this->setErrorMessage('Unable to load font file.', static::RDIERROR_WMT_FONT_NOTEXISTS);
            return false;
        }
        
        $Watermark = new Gd\Watermark($this);
        $result = $Watermark->applyText($wm_txt_text, $wm_txt_font_path, $wm_txt_start_x, $wm_txt_start_y, $wm_txt_font_size, $wm_txt_font_color, $wm_txt_font_alpha, $options);
        unset($Watermark);
        if (false === $result) {
            return false;
        }
        unset($result);

        $this->setStatusSuccess();
        return true;
    }// watermarkText


}
