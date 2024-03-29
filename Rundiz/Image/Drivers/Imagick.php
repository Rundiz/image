<?php
/**
 * PHP Image manipulation class.
 * 
 * @package Image
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * @link http://php.net/manual/en/book.imagick.php Reference of PHP Imagick classes/methods.
 * @link https://pecl.php.net/package/imagick Reference of PECL Imagick.
 */


namespace Rundiz\Image\Drivers;

use Rundiz\Image\AbstractImage;

/**
 * ImageMagick driver for image manipulation.
 *
 * @since 3.0
 * @property-read \Imagick $ImagickFirstFrame
 * @property-read int $source_image_frames
 */
class Imagick extends AbstractImage
{


    /**
     * @var int Imagick filter for use with resize image
     */
    public $imagick_filter = \Imagick::FILTER_LANCZOS;
    /**
     * @var bool Set to true to allow to save processed image as animated gif (if source is animated gif). Set to false to save as non-animated gif (if source is animated gif). For non-animated gif source file this option has no effect.
     */
    public $save_animate_gif = true;
    /**
     * @var int Set custom watermark text baseline for Imagick only due to rendered font size and baseline between GD and Imagick are different. Default is 0.
     */
    public $imagickWatermarkTextBaseline = 0;

    /**
     * @var \Imagick Contain Imagick object.
     */
    public $Imagick;
    /**
     * @var \Imagick Imagick first frame object. (works with animated gif only.)
     */
    protected $ImagickFirstFrame;

    /**
     * @var int Number of key frames of image. If the image is animated gif this will be total number of frames otherwise it is just 1 frame.
     */
    protected $source_image_frames = 0;

    /**
     * @var \Imagick Imagick for watermark image.
     */
    public $ImagickWatermark;


    /**
     * {@inheritDoc}
     */
    public function __construct($source_image_path)
    {
        parent::__construct($source_image_path);

        // verify php imagick extension and image magick version
        $this->verifyImagickVersion();

        if ($this->status == false && ($this->statusCode != null || $this->status_msg != null)) {
            return false;
        } else {
            $this->buildSourceImageData($source_image_path);
            return true;
        }
    }// __construct


    /**
     * Class de-constructor.
     */
    public function __destruct()
    {
        $this->clear();
    }// __destruct


    /**
     * Build source image data (special for Image Magick).
     * 
     * @param string $source_image_path Path to source image.
     * @return bool Return true on success, false on failed.
     */
    protected function buildSourceImageData($source_image_path)
    {
        if ($this->source_image_data == null) {
            parent::buildSourceImageData($source_image_path);
        }

        if ($this->status == false && ($this->statusCode != null || $this->status_msg != null)) {
            return false;
        }

        try {
            $Imagick = new \Imagick(realpath($source_image_path));
            $i = $Imagick->getNumberImages();
            $this->source_image_frames = $i;
            $this->source_image_data = array_merge($this->source_image_data, ['frames' => $i]);
            $Imagick->clear();
        } catch (\Error $err) {
            return false;
        } catch (\Exception $ex) {
            return false;
        }
        unset($i, $Imagick);

        return true;
    }// buildSourceImageData


    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        if (is_object($this->Imagick)) {
            $this->Imagick->clear();
        }

        if (is_object($this->ImagickFirstFrame)) {
            $this->ImagickFirstFrame->clear();
        }

        if (is_object($this->ImagickWatermark)) {
            $this->ImagickWatermark->clear();
        }

        $this->Imagick = null;
        $this->ImagickFirstFrame = null;
        $this->ImagickWatermark = null;

        $this->source_image_frames = 0;

        parent::clear();
        parent::buildSourceImageData($this->source_image_path);
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

        // setup source image object (Imagick object)
        if (false === $this->setupSourceImageObject()) {
            return false;
        }

        // check previous step contain errors?
        if ($this->isPreviousError() === true) {
            return false;
        }

        // convert width and height to integer
        list($width, $height) = $this->normalizeWidthHeight($width, $height);

        $Crop = new Imagick\Crop($this);
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

        // setup source image object (Imagick object)
        if (false === $this->setupSourceImageObject()) {
            return false;
        }

        // check previous step contain errors?
        if ($this->isPreviousError() === true) {
            return false;
        }

        // convert width and height to integer
        list($width, $height) = $this->normalizeWidthHeight($width, $height);

        $Resize = new Imagick\Resize($this);
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

        $Rotate = new Imagick\Rotate($this);
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

        // setup source object in case that it was not set.
        if ($this->Imagick == null) {
            $this->setupSourceImageObject();
        }

        // check previous step contain errors?
        if ($this->isPreviousError() === true) {
            return false;
        }

        $Save = new Imagick\Save($this);
        return $Save->execute($file_name);
    }// save


    /**
     * Setup source image object.
     * After calling this the Imagick property will get new Image Magick object if it does not set before.
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

        if ($this->Imagick == null || !is_object($this->Imagick)) {
            try {
                $this->Imagick = new \Imagick($this->source_image_path);
            } catch (\Exception $ex) {
            }

            if ($this->Imagick != null && is_object($this->Imagick)) {
                $this->setStatusSuccess();
                return true;
            } else {
                $this->setErrorMessage('Unable to set source from this kind of image.', static::RDIERROR_SRC_UNKNOWN);
                return false;
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

        // in case that it was called new Imagick object and then save without any modification.
        if ($this->Imagick == null) {
            // use resizeNoRatio instead of setupSourceImageObject in case that source file is animated gif it can setup first frame object.
            $this->resizeNoRatio($this->source_image_width, $this->source_image_height);
        }

        // check previous step contain errors?
        if ($this->isPreviousError() === true) {
            return false;
        }

        $Show = new Imagick\Show($this);
        return $Show->execute($file_ext);
    }// show


    /**
     * Verify PHP Imagick extension and Image Magick version.
     * 
     * @return bool Return true on success, false on failed.
     */
    private function verifyImagickVersion()
    {
        if (extension_loaded('imagick') !== true) {
            // imagick extension was not loaded.
            $this->setErrorMessage('The PHP Imagick extension was not loaded.', static::RDIERROR_IMAGICK_NOTLOAD);
            return false;
        }

        // verify basic requirements ( https://www.php.net/manual/en/imagick.requirements.php ).
        $imagickVersion = phpversion('imagick');
        if (version_compare($imagickVersion, '3.0', '<')) {
            // if Imagick version is less than 3.
            // it cannot use `\Imagick::getVersion()` method.
            $this->setErrorMessage('Require at least Imagick version 3.0', static::RDIERROR_IMAGICK_NOTMEETREQUIREMENT);
            unset($imagickVersion);
            return false;
        }
        unset($imagickVersion);

        $immVA = \Imagick::getVersion();// get Image Magick version array.
        if (!is_array($immVA) || !array_key_exists('versionString', $immVA)) {
            // don't know Image Magick version.
            $this->setErrorMessage('Unable to verify Image Magick version.', static::RDIERROR_IMAGICK_VERSIONUNKNOW);
            unset($immVA);
            return false;
        } else {
            // know Image Magick version.
            // verify Image Magick version.
            preg_match('/ImageMagick ([0-9]+\.[0-9]+\.[0-9]+)/', $immVA['versionString'], $matches);
            unset($immVA);
            if (!is_array($matches) || !array_key_exists(1, $matches)) {
                // if not found version number.
                $this->setErrorMessage('Unable to verify Image Magick version.', static::RDIERROR_IMAGICK_VERSIONUNKNOW);
                unset($matches);
                return false;
            } else {
                if (version_compare($matches[1], '6.2.4', '<')) {
                    // if Image Magick version is lower than requirement in PHP page.
                    $this->setErrorMessage('Require at least Image Magick 6.2.4.', static::RDIERROR_IMAGEMAGICK_NOTMEETREQUIREMENT);
                    unset($matches);
                    return false;
                }
            }
        }// endif; Image Magick version
        // end verify basic requirements. -------------------------------------------------------------------

        unset($imagick_extension_version);
        if (!$this->isPreviousError()) {
            $this->setStatusSuccess();
        }
        return true;
    }// verifyImagickVersion


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
        $Watermark = new Imagick\Watermark($this);
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
                $this->Imagick->getImageWidth(),
                $this->Imagick->getImageHeight(),
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
     * @param int $wm_img_start_y Position to begin in y axis. The value is integer or 'top', 'middle', 'bottom'.
     * @return bool Return true on success, false on failed.
     */
    private function watermarkImageProcess($wm_img_path, $wm_img_start_x = 0, $wm_img_start_y = 0)
    {
        if ($this->isPreviousError()) {
            return false;
        }

        $Watermark = new Imagick\Watermark($this);
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
        
        $Watermark = new Imagick\Watermark($this);
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
