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
     * @var resource|\GdImage|null|false Image resource identifier
     */
    protected $destination_image_object = null;
    /**
     * @var resource|\GdImage|null|false Image resource identifier
     */
    protected $source_image_object = null;

    /**
     * @var resource|\GdImage|null|false Image resource identifier for watermark image.
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
        if ($this->isResourceOrGDObject($this->destination_image_object) && version_compare(PHP_VERSION, '8.0', '<')) {
            imagedestroy($this->destination_image_object);
        }

        if ($this->isResourceOrGDObject($this->source_image_object) && version_compare(PHP_VERSION, '8.0', '<')) {
            imagedestroy($this->source_image_object);
        }

        if ($this->isResourceOrGDObject($this->watermark_image_object) && version_compare(PHP_VERSION, '8.0', '<')) {
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
     * No operation.
     * 
     * This will be create new image object that has transparency and copy source image to it.
     * 
     * @since 3.1.4
     */
    private function noOp()
    {
        if (false === $this->isClassSetup()) {
            return false;
        }

        // setup source and destination image objects
        if (false === $this->setupSourceImageObject()) {
            return false;
        }
        if (false === $this->setupDestinationImageObjectWithSize($this->source_image_width, $this->source_image_height)) {
            return false;
        }

        // check previous step contain errors?
        if ($this->isPreviousError() === true) {
            return false;
        }

        $NoOp = new Gd\NoOp($this);
        $result = $NoOp->execute();
        unset($NoOp);
        if (false === $result) {
            return false;
        }
        unset($result);

        // set new value to properties
        $this->last_modified_image_height = $this->source_image_height;
        $this->last_modified_image_width = $this->source_image_width;
        $this->destination_image_height = $this->source_image_height;
        $this->destination_image_width = $this->source_image_width;

        return true;
    }// noOp


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

        $source_image_width = $this->source_image_width;
        if ($this->last_modified_image_width > 0) {
            $source_image_width = $this->last_modified_image_width;
        }
        $source_image_height = $this->source_image_height;
        if ($this->last_modified_image_height > 0) {
            $source_image_height = $this->last_modified_image_height;
        }

        if (false === $this->setupDestinationImageObjectWithSize($source_image_width, $source_image_height)) {
            return false;
        }
        unset($source_image_height, $source_image_width);

        // check previous step contain errors?
        if ($this->isPreviousError() === true) {
            return false;
        }

        $degree = $this->normalizeDegree($degree);

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
        if (!$this->isResourceOrGDObject($this->destination_image_object)) {
            $this->noOp();
        }

        // check previous step contain errors?
        if ($this->isPreviousError() === true) {
            return false;
        }

        $this->verifyQualityValue();

        $Save = new Gd\Save($this);
        return $Save->execute($file_name);
    }// save


    /**
     * Setup new canvas or "destination image object".
     * 
     * Set in width and height for use with resize, crop.
     * 
     * @param int $width Destination image object width.
     * @param int $height Destination image object height.
     * @return bool Return `true` on success, `false` on failure.
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
            imagesavealpha($this->destination_image_object, true);
            $this->fillTransparentOnObject($this->destination_image_object);
        }

        // come to this means destination image object is already set.
        $this->setStatusSuccess();
        return true;
    }// setupDestinationImageObjectWithSize


    /**
     * Setup source image object.
     * 
     * In case that this is first process, it will be create image object (or resource in older version of PHP) from image file.<br>
     * Otherwise, it will be chaining image object from previous destination (or previous process).
     * 
     * @return bool Return `true` on success, `false` on failure.
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
            // if there is no source image object.
            if ($this->isResourceOrGDObject($this->destination_image_object)) {
                // if there is previous "destination image object".
                // chain to be source image object.
                $this->source_image_object = $this->destination_image_object;
                $this->destination_image_object = null;

                $this->setStatusSuccess();
                return true;
            } else {
                // if there is no previous "destination image object".
                // create new one from image file.
                $srcObject = $this->setupSourceFromFile($this->source_image_path, $this->source_image_type);
                if ($this->isResourceOrGDObject($srcObject)) {
                    $this->source_image_object = $srcObject;
                }
                unset($srcObject);

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

        // in case that it was called new Gd object and then show without any modification.
        if (!$this->isResourceOrGDObject($this->destination_image_object)) {
            $this->noOp();
        }

        // check previous step contain errors?
        if ($this->isPreviousError() === true) {
            return false;
        }

        $this->verifyQualityValue();

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

        // in case that it was called new Gd object and then save without any modification.
        if (!$this->isResourceOrGDObject($this->destination_image_object)) {
            $this->noOp();
        }

        // check previous step contain errors?
        if ($this->isPreviousError() === true) {
            return false;
        }

        // setup watermark object for use later.
        $Watermark = new Gd\Watermark($this);
        $result = $Watermark->applyImage($wm_img_path, $wm_img_start_x, $wm_img_start_y, $options);
        unset($Watermark);
        if (false === $result) {
            return false;
        }
        unset($result);

        $this->setStatusSuccess();

        return true;
    }// watermarkImage


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

        // check watermark font path exists
        if (!is_file($wm_txt_font_path)) {
            $this->setErrorMessage('Unable to load font file.', static::RDIERROR_WMT_FONT_NOTEXISTS);
            return false;
        }

        // in case that it was called new Gd object and then save without any modification.
        if (!$this->isResourceOrGDObject($this->destination_image_object)) {
            $this->noOp();
        }

        // check previous step contain errors?
        if ($this->isPreviousError() === true) {
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
