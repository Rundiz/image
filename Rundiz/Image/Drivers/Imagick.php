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

use Rundiz\Image\ImageAbstractClass;

/**
 * ImageMagick driver for image manipulation.
 *
 * @since 3.0
 */
class Imagick extends ImageAbstractClass
{


    /**
     * @var integer Imagick filter for use with resize image
     */
    public $imagick_filter = \Imagick::FILTER_LANCZOS;
    /**
     * @var boolean Set to true to allow to save processed image as animated gif (if source is animated gif). Set to false to save as non-animated gif (if source is animated gif). For non-animated gif source file this option has no effect.
     */
    public $save_animate_gif = true;

    /**
     * @var \Imagick Contain Imagick object.
     */
    public $Imagick;
    /**
     * @var \Imagick Imagick first frame object. (works with animated gif only.)
     */
    private $ImagickFirstFrame;

    /**
     * @var integer Number of key frames of image. If the image is animated gif this will be total number of frames otherwise it is just 1 frame.
     */
    protected $source_image_frames = 0;

    /**
     * @var \Imagick Imagick for watermark image.
     */
    public $ImagickWatermark;
    /**
     * @var string Watermark image type. See more at http://php.net/manual/en/function.getimagesize.php The numbers of these extensions are: 1=gif, 2=jpg, 3=png
     */
    private $watermark_image_type;
    /**
     * @var integer Watermark image width
     */
    private $watermark_image_width;
    /**
     * @var integer Watermark image height
     */
    private $watermark_image_height;


    /**
     * {@inheritDoc}
     */
    public function __construct($source_image_path)
    {
        parent::__construct($source_image_path);

        // verify php imagick extension and image magick version
        $this->verifyImagickVersion();

        if ($this->status == false && $this->status_msg != null) {
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
     * @return boolean Return true on success, false on failed. Call to status_msg property to see the details on failure.
     */
    protected function buildSourceImageData($source_image_path)
    {
        if ($this->source_image_data == null) {
            parent::buildSourceImageData($source_image_path);
        }

        if ($this->status == false && $this->status_msg != null) {
            return false;
        }

        $Imagick = new \Imagick(realpath($source_image_path));
        $i = $Imagick->getNumberImages();
        $this->source_image_frames = $i;
        $this->source_image_data = array_merge($this->source_image_data, array('frames' => $i));
        $Imagick->clear();
        unset($i, $Imagick);

        return true;
    }// buildSourceImageData


    /**
     * Calculate counter clockwise degree.
     * In GD 90 degree is 270 in Imagick. This function help to make it working together very well.
     * 
     * @param integer $value Degrees.
     * @return integer Return opposite degrees.
     */
    private function calculateCounterClockwise($value)
    {
        if ($value == 0 || $value == 180) {
            return $value;
        } elseif ($value == 360) {
            return 0;
        }

        if ($value < 0 || $value > 360) {
            $value = 90;
        }

        $total_degree = 360;
        $output = intval($total_degree - $value);
        return $output;
    }// calculateCounterClockwise


    /**
     * Calculate startX position of center
     * 
     * @param integer $obj_width Destination image object size.
     * @param integer $canvas_width Canvas size.
     * @return integer Calculated size.
     */
    private function calculateStartXOfCenter($obj_width = '', $canvas_width = '') 
    {
        if (!is_numeric($obj_width) || !is_numeric($canvas_width)) {
            return 0;
        }

        return intval(round(($canvas_width/2)-($obj_width/2)));
    }// calculateStartXOfCenter


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
        $this->watermark_image_height = null;
        $this->watermark_image_type = null;
        $this->watermark_image_width = null;

        $this->status = false;
        $this->status_msg = null;

        $this->destination_image_height = null;
        $this->destination_image_width = null;
        $this->last_modified_image_height = null;
        $this->last_modified_image_width = null;

        parent::buildSourceImageData($this->source_image_path);
        $this->buildSourceImageData($this->source_image_path);
    }// clear


    /**
     * Convert alpha number (0 - 127) to rgba value (1.0 - 0).
     * 
     * @param integer $number Alpha number (0 to 127).
     * @return string Return rgba value (1.0 to 0).
     */
    private function convertAlpha127ToRgba($number)
    {
        $alpha_min = 0; // 100%
        $alpha_max = 127; // 0%

        if ($number < $alpha_min) {
            $number = 0;
        } elseif ($number > $alpha_max) {
            $number = 127;
        }

        $find_percent = ($alpha_max - $number) / ($alpha_max / 100);

        unset($alpha_max, $alpha_min);
        return number_format(($find_percent / 100), 2);
    }// convertAlpha127ToRgba


    /**
     * {@inheritDoc}
     */
    public function crop($width, $height, $start_x = '0', $start_y = '0', $fill = 'transparent')
    {
        if (false === $this->isClassSetup()) {
            return false;
        }

        // convert width and height to integer
        $height = intval($height);
        $width = intval($width);

        // width and height must larger than 0
        if ($height <= 0) {
            $height = 100;
        }
        if ($width <= 0) {
            $width = 100;
        }

        // setup source image object (Imagick object)
        if (false === $this->setupSourceImageObject()) {
            return false;
        }

        // check previous step contain errors?
        if ($this->isPreviousError() === true) {
            return false;
        }

        // calculate start x while $start_x was set as 'center'.
        if ($start_x === 'center') {
            $canvas_width = $this->Imagick->getImageWidth();
            $object_width = $width;

            $start_x = $this->calculateStartXOfCenter($object_width, $canvas_width);

            unset($canvas_width, $object_width);
        } else {
            $start_x = intval($start_x);
        }

        // calculate start y while $start_y was set as 'middle'
        if ($start_y === 'middle') {
            $canvas_height = $this->Imagick->getImageHeight();
            $object_height = $height;

            $start_y = $this->calculateStartXOfCenter($object_height, $canvas_height);

            unset($canvas_height, $object_height);
        } else {
            $start_y = intval($start_y);
        }

        // set color
        $black = new \ImagickPixel('black');
        $white = new \ImagickPixel('white');
        $transwhite = new \ImagickPixel('rgba(255, 255, 255, 0)');

        if ($fill != 'transparent' && $fill != 'white' && $fill != 'black') {
            $fill = 'transparent';
        }

        // begins crop
        if ($this->source_image_type == '1') {
            // gif
            if ($this->source_image_frames > 1) {
                $this->Imagick = $this->Imagick->coalesceImages();
                if (is_object($this->Imagick)) {
                    $i = 1;
                    foreach ($this->Imagick as $Frame) {
                        $Frame->cropImage($width, $height, $start_x, $start_y);
                        $Frame->extentImage($width, $height, $this->calculateStartXOfCenter($width, $this->Imagick->getImageWidth()), $this->calculateStartXOfCenter($height, $this->Imagick->getImageHeight()));
                        $Frame->setImagePage(0, 0, 0, 0);
                        if ($fill != 'transparent') {
                            $Frame->setImageBackgroundColor($$fill);
                            $Frame->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
                        }
                        if ($i == 1) {
                            $this->ImagickFirstFrame = $Frame->getImage();
                        }
                        $i++;
                    }
                    unset($Frame, $i);
                }
            } else {
                $this->Imagick->cropImage($width, $height, $start_x, $start_y);
                $this->Imagick->setImagePage(0, 0, 0, 0);
                $this->Imagick->extentImage($width, $height, $this->calculateStartXOfCenter($width, $this->Imagick->getImageWidth()), $this->calculateStartXOfCenter($height, $this->Imagick->getImageHeight()));
                $this->ImagickFirstFrame = null;
                if ($fill != 'transparent') {
                    $this->Imagick->setImageBackgroundColor($$fill);
                    $this->Imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
                }
            }
        } elseif ($this->source_image_type == '2' || $this->source_image_type == '3') {
            // jpg OR png
            $this->Imagick->cropImage($width, $height, $start_x, $start_y);
            $this->Imagick->setImageBackgroundColor($transwhite);// for transparent png and allow to fill other bg color than black in jpg.
            $this->Imagick->extentImage($width, $height, $this->calculateStartXOfCenter($width, $this->Imagick->getImageWidth()), $this->calculateStartXOfCenter($height, $this->Imagick->getImageHeight()));

            if ($fill != 'transparent') {
                $this->Imagick->setImageBackgroundColor($$fill);
                $this->Imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
            }
        } else {
            $this->status = false;
            $this->status_msg = 'Unable to crop this kind of image.';
            return false;
        }

        $this->last_modified_image_height = $height;
        $this->last_modified_image_width = $width;
        $this->destination_image_height = $height;
        $this->destination_image_width = $width;

        $black->destroy();
        $transwhite->destroy();
        $white->destroy();
        unset($black, $fill, $transwhite, $white);
        return true;
    }// crop


    /**
     * {@inheritDoc}
     */
    public function getImageSize()
    {
        return array(
            'height' => $this->source_image_height,
            'width' => $this->source_image_width,
        );
    }// getImageSize


    /**
     * Check is previous operation contain error?
     * 
     * @return boolean Return true if there is some error, false if there is not.
     */
    private function isPreviousError()
    {
        if ($this->status == false && $this->status_msg != null) {
            return true;
        }
        return false;
    }// isPreviousError


    /**
     * {@inheritDoc}
     */
    public function resize($width, $height)
    {
        if (false === $this->isClassSetup()) {
            return false;
        }

        $sizes = $this->calculateImageSizeRatio($width, $height);

        if (
            !is_array($sizes) || 
            (
                is_array($sizes) && 
                (!array_key_exists('height', $sizes) || !array_key_exists('width', $sizes))
            )
        ) {
            $this->status = false;
            $this->status_msg = 'Unable to calculate sizes, please try to calculate on your own and call to resizeNoRatio instead.';
            return false;
        }

        return $this->resizeNoRatio($sizes['width'], $sizes['height']);
    }// resize


    /**
     * {@inheritDoc}
     */
    public function resizeNoRatio($width, $height)
    {
        if (false === $this->isClassSetup()) {
            return false;
        }

        // convert width and height to integer
        $height = intval($height);
        $width = intval($width);

        // width and height must larger than 0
        if ($height <= 0) {
            $height = 100;
        }
        if ($width <= 0) {
            $width = 100;
        }

        // setup source image object (Imagick object)
        if (false === $this->setupSourceImageObject()) {
            return false;
        }

        // check previous step contain errors?
        if ($this->isPreviousError() === true) {
            return false;
        }

        // begins resize
        if ($this->source_image_type == '1') {
            // gif
            if ($this->source_image_frames > 1) {
                $this->Imagick = $this->Imagick->coalesceImages();
                if (is_object($this->Imagick)) {
                    $i = 1;
                    foreach ($this->Imagick as $Frame) {
                        $Frame->resizeImage($width, $height, $this->imagick_filter, 1);
                        $Frame->setImagePage(0, 0, 0, 0);
                        if ($i == 1) {
                            $this->ImagickFirstFrame = $Frame->getImage();
                        }
                        $i++;
                    }
                    unset($Frame, $i);
                }
            } else {
                $this->Imagick->resizeImage($width, $height, $this->imagick_filter, 1);
                $this->Imagick->setImagePage(0, 0, 0, 0);
                $this->ImagickFirstFrame = null;
            }
        } elseif ($this->source_image_type == '2' || $this->source_image_type == '3') {
            // jpg or png
            $this->Imagick->resizeImage($width, $height, $this->imagick_filter, 1);
        } else {
            $this->status = false;
            $this->status_msg = 'Unable to resize this kind of image.';
            return false;
        }

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

        // check degree
        $allowed_flip = array('hor', 'vrt', 'horvrt');
        if (is_numeric($degree)) {
            $degree = intval($degree);
        } elseif (!is_numeric($degree) && !in_array($degree, $allowed_flip)) {
            $degree = 90;
        }
        unset($allowed_flip);

        // setup source image object
        if (false === $this->setupSourceImageObject()) {
            return false;
        }

        // check previous step contain errors?
        if ($this->isPreviousError() === true) {
            return false;
        }

        // begins rotate.
        if (is_int($degree)) {
            // rotate by degree
            switch ($this->source_image_type) {
                case '1':
                    // gif
                    if ($this->source_image_frames > 1) {
                        $this->Imagick = $this->Imagick->coalesceImages();
                        if (is_object($this->Imagick)) {
                            $i = 1;
                            foreach ($this->Imagick as $Frame) {
                                $Frame->rotateImage(new \ImagickPixel('rgba(255, 255, 255, 0)'), $this->calculateCounterClockwise($degree));
                                $Frame->setImagePage(0, 0, 0, 0);
                                if ($i == 1) {
                                    $this->ImagickFirstFrame = $Frame->getImage();
                                }
                                $i++;
                            }
                            unset($Frame, $i);
                        }
                    } else {
                        $this->Imagick->rotateImage(new \ImagickPixel('rgba(255, 255, 255, 0)'), $this->calculateCounterClockwise($degree));
                        $this->Imagick->setImagePage(0, 0, 0, 0);
                        $this->ImagickFirstFrame = null;
                    }
                    break;
                case '2':
                    // jpg
                case '3':
                    // png
                    $this->Imagick->rotateImage(new \ImagickPixel('rgba(255, 255, 255, 0)'), $this->calculateCounterClockwise($degree));
                    break;
                default:
                    $this->status = false;
                    $this->status_msg = 'Unable to rotate this kind of image.';
                    return false;
            }

            $this->destination_image_height = $this->Imagick->getImageHeight();
            $this->destination_image_width = $this->Imagick->getImageWidth();

            $this->last_modified_image_height = $this->destination_image_height;
            $this->last_modified_image_width = $this->destination_image_width;
            $this->source_image_height = $this->destination_image_height;
            $this->source_image_width = $this->destination_image_width;
        } else {
            // flip image
            switch ($this->source_image_type) {
                case '1':
                    if ($this->source_image_frames > 1) {
                        $this->Imagick = $this->Imagick->coalesceImages();
                        if (is_object($this->Imagick)) {
                            $i = 1;
                            foreach ($this->Imagick as $Frame) {
                                if ($degree === 'hor') {
                                    $Frame->flopImage();
                                } elseif ($degree == 'vrt') {
                                    $Frame->flipImage();
                                } else {
                                    $Frame->flopImage();
                                    $Frame->flipImage();
                                }
                                $Frame->setImagePage(0, 0, 0, 0);
                                if ($i == 1 && $this->ImagickFirstFrame == null) {
                                    $this->ImagickFirstFrame = $Frame->getImage();
                                }
                                $i++;
                            }
                            unset($Frame, $i);
                        }
                    } else {
                        if ($degree === 'hor') {
                            $this->Imagick->flopImage();
                        } elseif ($degree == 'vrt') {
                            $this->Imagick->flipImage();
                        } else {
                            $this->Imagick->flopImage();
                            $this->Imagick->flipImage();
                        }
                        $this->ImagickFirstFrame = null;
                    }
                    break;
                case '2':
                    // jpg
                case '3':
                    // png
                    if ($degree === 'hor') {
                        $this->Imagick->flopImage();
                    } elseif ($degree == 'vrt') {
                        $this->Imagick->flipImage();
                    } else {
                        $this->Imagick->flopImage();
                        $this->Imagick->flipImage();
                    }
                    break;
                default:
                    $this->status = false;
                    $this->status_msg = 'Unable to flip this kind of image.';
                    return false;
            }

            $this->destination_image_height = $this->Imagick->getImageHeight();
            $this->destination_image_width = $this->Imagick->getImageWidth();

            $this->last_modified_image_height = $this->destination_image_height;
            $this->last_modified_image_width = $this->destination_image_width;
            $this->source_image_height = $this->destination_image_height;
            $this->source_image_width = $this->destination_image_width;
        }

        if (!$this->isPreviousError()) {
            $this->status = true;
            $this->status_msg = null;
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

        // set the real path to save file name in case that it was set as relative path.
        $file_name_exp = explode('/', str_ireplace('\\', '/', $file_name));
        $file_name_only = $file_name_exp[count($file_name_exp)-1];
        unset($file_name_exp[count($file_name_exp)-1]);
        $save_folder = implode(DIRECTORY_SEPARATOR, $file_name_exp);
        $real_file_name = realpath($save_folder) . DIRECTORY_SEPARATOR . $file_name_only;
        unset($file_name_exp, $file_name_only);

        if (strpos($file_name, '.') !== false) {
            $file_name_exp = explode('.', $file_name);
            if (is_array($file_name_exp) && isset($file_name_exp[count($file_name_exp)-1])) {
                $file_ext = $file_name_exp[count($file_name_exp)-1];
            } else {
                $file_ext = str_replace('.', '', $this->source_image_ext);
            }
        } else {
            $file_ext = str_replace('.', '', $this->source_image_ext);
        }
        unset($file_name_exp);
        $file_ext = str_ireplace('jpeg', 'jpg', $file_ext);
        $file_ext = ltrim($file_ext, '.');

        $check_file_ext = strtolower($file_ext);

        // setup source object in case that it was not set.
        if ($this->Imagick == null) {
            $this->setupSourceImageObject();
        }

        // check previous step contain errors?
        if ($this->isPreviousError() === true) {
            return false;
        }

        // save to file. each image types use different ways to save.
        if ($check_file_ext == 'gif') {
            if ($this->source_image_type == '3') {
                // source file is png
                // convert from transparent to white before save
                $this->Imagick->setImagePage(0, 0, 0, 0);
                $this->Imagick->setImageBackgroundColor('white');
                $this->Imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
                $this->Imagick = $this->Imagick->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);
            }

            if ($this->source_image_type == '1' && $this->save_animate_gif === true) {
                // source file is gif and allow to save animated
                $save_result = $this->Imagick->writeImages($real_file_name, true);
            } else {
                if ($this->source_image_frames > 1 && is_object($this->ImagickFirstFrame)) {
                    // if source image is animated gif and save to non-animated gif, get the first frame.
                    $this->Imagick->clear();
                    $this->Imagick = $this->ImagickFirstFrame;
                    $this->ImagickFirstFrame = null;
                }

                if ($this->source_image_type !== '1') {
                    // source image is other than gif, it is required to set image page.
                    $this->Imagick->setImagePage(0, 0, 0, 0);
                }

                $save_result = $this->Imagick->writeImage($real_file_name);
            }
        } elseif ($check_file_ext == 'jpg') {
            if ($this->source_image_type == '1') {
                // source file is gif
                if ($this->source_image_frames > 1 && is_object($this->ImagickFirstFrame)) {
                    // if source image is animated gif and save to non-animated gif, get the first frame.
                    $this->Imagick->clear();
                    $this->Imagick = $this->ImagickFirstFrame;
                    $this->ImagickFirstFrame = null;
                }

                // covnert from transparent to white before save
                $this->Imagick->setImageBackgroundColor('white');
                $this->Imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
                $this->Imagick = $this->Imagick->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);
            } elseif ($this->source_image_type == '3') {
                // source file is png
                // convert from transparent to white before save
                $this->Imagick->setImagePage(0, 0, 0, 0);
                $this->Imagick->setImageBackgroundColor('white');
                $this->Imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
                $this->Imagick = $this->Imagick->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);
            }

            $this->jpg_quality = intval($this->jpg_quality);
            if ($this->jpg_quality < 0 || $this->jpg_quality > 100) {
                $this->jpg_quality = 100;
            }

            $this->Imagick->setImageCompressionQuality($this->jpg_quality);
            $save_result = $this->Imagick->writeImage($real_file_name);
        } elseif ($check_file_ext == 'png') {
            if ($this->source_image_type == '1') {
                // source file is gif
                if ($this->source_image_frames > 1 && is_object($this->ImagickFirstFrame)) {
                    // if source image is animated gif and save to non-animated gif, get the first frame.
                    $this->Imagick->clear();
                    $this->Imagick = $this->ImagickFirstFrame;
                    $this->ImagickFirstFrame = null;
                }

                // covnert from transparent to white before save
                $this->Imagick->setImageBackgroundColor('white');
                $this->Imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
                $this->Imagick = $this->Imagick->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);
            }

            // png compression for imagick does not work!!!
            $this->png_quality = intval($this->png_quality);
            if ($this->png_quality < 0 || $this->png_quality > 9) {
                $this->png_quality = 0;
            }

            $save_result = $this->Imagick->writeImage($real_file_name);
        } else {
            $this->status = false;
            $this->status_msg = sprintf('Unable to save this kind of image. (%s)', $check_file_ext);
            return false;
        }

        if (isset($save_result) && $save_result !== false) {
            $this->status = true;
            $this->status_msg = null;
            return true;
        } else {
            $this->status = false;
            $this->status_msg = 'Failed to save the image.';
            return false;
        }
    }// save


    /**
     * Setup source image object.
     * After calling this the Imagick property will get new Image Magick object if it does not set before.
     * 
     * @return boolean Return true on success, false on failed. Call to status_msg property to see the details on failure.
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
            $this->Imagick = new \Imagick($this->source_image_path);

            if ($this->Imagick != null && is_object($this->Imagick)) {
                $this->status = true;
                $this->status_msg = null;
                return true;
            } else {
                $this->status = false;
                $this->status_msg = 'Unable to set source from this kind of image.';
                return false;
            }
        }

        // come to this means source image object is already set.
        $this->status = true;
        $this->status_msg = null;
        return true;
    }// setupSourceImageObject


    /**
     * Setup watermark image object.
     * After calling this the ImagickWatermark will get new Image Magick object if it does not set before.
     * 
     * @param string $wm_img_path Path to watermark image.
     * @return boolean Return true on success, false on failed. Call to status_msg property to see the details on failure.
     */
    private function setupWatermarkImageObject($wm_img_path)
    {
        if (!is_file($wm_img_path)) {
            $this->status = false;
            $this->status_msg = 'Watermark image was not found.';
            return false;
        }
        $wm_img_path = realpath($wm_img_path);

        list($wm_width, $wm_height, $wm_type) = getimagesize($wm_img_path);

        if ($wm_height == null || $wm_width == null || $wm_type == null) {
            $this->status = false;
            $this->status_msg = 'Watermark is not an image.';
            return false;
        }

        if (is_object($this->ImagickWatermark)) {
            $this->ImagickWatermark->clear();
            $this->ImagickWatermark = null;
        }

        if ($this->ImagickWatermark == null || !is_object($this->ImagickWatermark)) {
            $this->ImagickWatermark = new \Imagick($wm_img_path);

            if ($this->ImagickWatermark == null || !is_object($this->ImagickWatermark)) {
                $this->status = false;
                $this->status_msg = 'Unable to set watermark from this kind of image.';
                return false;
            }
        }

        $this->watermark_image_height = $wm_height;
        $this->watermark_image_width = $wm_width;
        $this->watermark_image_type = $wm_type;

        unset($wm_height, $wm_img_path, $wm_type, $wm_width);
        $this->status = true;
        $this->status_msg = null;
        return true;
    }// setupWatermarkImageObject


    /**
     * {@inheritDoc}
     */
    public function show($file_ext = '')
    {
        if (false === $this->isClassSetup()) {
            return false;
        }

        if ($file_ext == null) {
            $file_ext = str_replace('.', '', $this->source_image_ext);
        }
        $file_ext = str_ireplace('jpeg', 'jpg', $file_ext);
        $file_ext = ltrim($file_ext, '.');

        $check_file_ext = strtolower($file_ext);

        // in case that it was called new Imagick object and then save without any modification.
        if ($this->Imagick == null) {
            // use resizeNoRatio instead of setupSourceImageObject in case that source file is animated gif it can setup first frame object.
            $this->resizeNoRatio($this->source_image_width, $this->source_image_height);
        }

        // check previous step contain errors?
        if ($this->isPreviousError() === true) {
            return false;
        }

        // show image to browser.
        // http://php.net/manual/en/imagick.getimageblob.php for single frame of image or non-animated picture.
        // http://php.net/manual/en/imagick.getimagesblob.php for animated gif.
        if ($check_file_ext == 'gif') {
            if ($this->source_image_type == '3') {
                // source file is png
                // convert from transparent to white before save
                $this->Imagick->setImagePage(0, 0, 0, 0);
                $this->Imagick->setImageBackgroundColor('white');
                $this->Imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
                $this->Imagick = $this->Imagick->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);
            }

            if ($this->source_image_type == '1' && $this->save_animate_gif === true) {
                // source file is gif and allow to show animated
                $show_result = $this->Imagick->getImagesBlob();
            } else {
                if ($this->source_image_frames > 1 && is_object($this->ImagickFirstFrame)) {
                    // if source image is animated gif and save to non-animated gif, get the first frame.
                    $this->Imagick->clear();
                    $this->Imagick = $this->ImagickFirstFrame;
                    $this->ImagickFirstFrame = null;
                }

                if ($this->source_image_type !== '1') {
                    // source image is other than gif, it is required to set image page.
                    $this->Imagick->setImagePage(0, 0, 0, 0);
                }

                $this->Imagick->setImageFormat('gif');
                $show_result = $this->Imagick->getImageBlob();
            }
        } elseif ($check_file_ext == 'jpg') {
            if ($this->source_image_type == '1') {
                // source file is gif
                if ($this->source_image_frames > 1 && is_object($this->ImagickFirstFrame)) {
                    // if source image is animated gif and save to non-animated gif, get the first frame.
                    $this->Imagick->clear();
                    $this->Imagick = $this->ImagickFirstFrame;
                    $this->ImagickFirstFrame = null;
                }

                // covnert from transparent to white before save
                $this->Imagick->setImageBackgroundColor('white');
                $this->Imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
                $this->Imagick = $this->Imagick->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);
            } elseif ($this->source_image_type == '3') {
                // source file is png
                // convert from transparent to white before save
                $this->Imagick->setImagePage(0, 0, 0, 0);
                $this->Imagick->setImageBackgroundColor('white');
                $this->Imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
                $this->Imagick = $this->Imagick->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);
            }

            $this->jpg_quality = intval($this->jpg_quality);
            if ($this->jpg_quality < 0 || $this->jpg_quality > 100) {
                $this->jpg_quality = 100;
            }

            $this->Imagick->setImageFormat('jpg');
            $this->Imagick->setImageCompressionQuality($this->jpg_quality);
            $show_result = $this->Imagick->getImageBlob();
        } elseif ($check_file_ext == 'png') {
            if ($this->source_image_type == '1') {
                // source file is gif
                if ($this->source_image_frames > 1 && is_object($this->ImagickFirstFrame)) {
                    // if source image is animated gif and save to non-animated gif, get the first frame.
                    $this->Imagick->clear();
                    $this->Imagick = $this->ImagickFirstFrame;
                    $this->ImagickFirstFrame = null;
                }

                // covnert from transparent to white before save
                $this->Imagick->setImageBackgroundColor('white');
                $this->Imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
                $this->Imagick = $this->Imagick->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);
            }

            // png compression for imagick does not work!!!
            $this->png_quality = intval($this->png_quality);
            if ($this->png_quality < 0 || $this->png_quality > 9) {
                $this->png_quality = 0;
            }

            $this->Imagick->setImageFormat('png');
            $show_result = $this->Imagick->getImageBlob();
        } else {
            $this->status = false;
            $this->status_msg = 'Unable to show this kind of image.';
            return false;
        }

        // clear
        unset($check_file_ext, $file_ext);

        if ($show_result !== false) {
            $this->status = true;
            $this->status_msg = null;
            // Because in PHP GD it is automatically show the image content by calling show() method without echo command.
            // But in PHP Imagick it must echo content that got from getImageBlob() of Imagick class, then we have to echo it here to make this image class work in the same way.
            echo $show_result;
            return true;
        } else {
            $this->status = false;
            $this->status_msg = 'Failed to show the image.';
            return false;
        }
    }// show


    /**
     * Verify PHP Imagick extension and Image Magick version.
     * 
     * @return boolean Return true on success, false on failed. Call to status_msg property to see the details on failure.
     */
    private function verifyImagickVersion()
    {
        if (extension_loaded('imagick') !== true) {
            // imagick extension was not loaded.
            $this->status = false;
            $this->status_msg = 'The PHP Imagick extension was not loaded.';
            return false;
        }

        $imagick_extension_version = phpversion('imagick');

        if (version_compare($imagick_extension_version, '3.4.0', '>=')) {
            // if PHP Imagick extension is equal or greater than 3.4
            $image_magick_v = \Imagick::getVersion();
            if (!is_array($image_magick_v) || (is_array($image_magick_v) && !array_key_exists('versionString', $image_magick_v))) {
                // don't know Image Magick version.
                $this->status = false;
                $this->status_msg = 'Unable to verify Image Magick version.';
                unset($image_magick_v);
                return false;
            } else {
                // verify Image Magick version.
                preg_match('/ImageMagick ([0-9]+\.[0-9]+\.[0-9]+)/', $image_magick_v['versionString'], $matches);
                unset($image_magick_v);
                if (!is_array($matches) || (is_array($matches) && !array_key_exists(1, $matches))) {
                    $this->status = false;
                    $this->status_msg = 'Unable to verify Image Magick version.';
                    unset($matches);
                    return false;
                } else {
                    if (version_compare($matches[1], '6.5.3', '<')) {
                        $this->status = false;
                        $this->status_msg = sprintf('This PHP Imagick extension (%s) require Image Magick 6.5.3+. You have Image Magick %s.', $imagick_extension_version, $matches[1]);
                        unset($matches);
                        return false;
                    }
                }
                unset($matches);

                // verify PHP version
                if (version_compare(phpversion(), '5.4', '<')) {
                    $this->status = false;
                    $this->status_msg = 'This PHP Imagick extension require PHP 5.4.0+.';
                    return false;
                }
            }
            // end verify for PHP Imagick extension v. 3.4+
        }

        unset($imagick_extension_version);
        if (!$this->isPreviousError()) {
            $this->status = true;
            $this->status_msg = null;
        }
        return true;
    }// verifyImagickVersion


    /**
     * {@inheritDoc}
     */
    public function watermarkImage($wm_img_path, $wm_img_start_x = 0, $wm_img_start_y = 0)
    {
        if (false === $this->isClassSetup()) {
            return false;
        }

        // check watermark image path exists
        if (!is_file($wm_img_path)) {
            $this->status = false;
            $this->status_msg = 'Watermark image was not found.';
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

        // if start x or y is NOT number, find the real position of start x or y from word left, center, right, top, middle, bottom
        if (!is_numeric($wm_img_start_x) || !is_numeric($wm_img_start_y)) {
            $this->setupWatermarkImageObject($wm_img_path);

            if ($this->isPreviousError()) {
                return false;
            }

            if (!is_numeric($wm_img_start_x)) {
                switch (strtolower($wm_img_start_x)) {
                    case 'center':
                        $image_width = $this->Imagick->getImageWidth();
                        $watermark_width = $this->ImagickWatermark->getImageWidth();

                        $wm_img_start_x = $this->calculateStartXOfCenter($watermark_width, $image_width);

                        unset($image_width, $watermark_width);
                        break;
                    case 'right':
                        $source_image_width = $this->source_image_width;
                        if ($this->last_modified_image_width != null) {
                            $source_image_width = $this->last_modified_image_width;
                        }

                        if ($source_image_width > ($this->watermark_image_width + 5)) {
                            $wm_img_start_x = intval($source_image_width - ($this->watermark_image_width + 5));
                        } else {
                            $wm_img_start_x = intval($source_image_width - $this->watermark_image_width);
                        }
                        unset($source_image_width);
                        break;
                    case 'left':
                    default:
                        $wm_img_start_x = 5;
                        break;
                }
            }

            if (!is_numeric($wm_img_start_y)) {
                switch (strtolower($wm_img_start_y)) {
                    case 'middle':
                        $image_height = $this->Imagick->getImageHeight();
                        $watermark_height = $this->ImagickWatermark->getImageHeight();

                        $wm_img_start_y = $this->calculateStartXOfCenter($watermark_height, $image_height);

                        unset($image_height, $watermark_height);
                        break;
                    case 'bottom':
                        $source_image_height = $this->source_image_height;
                        if ($this->last_modified_image_height != null) {
                            $source_image_height = $this->last_modified_image_height;
                        }

                        if ($source_image_height - ($this->watermark_image_height + 5) > '0') {
                            $wm_img_start_y = intval($source_image_height - ($this->watermark_image_height + 5));
                        } else {
                            $wm_img_start_y = intval($source_image_height - $this->watermark_image_height);
                        }
                        break;
                    case 'top':
                    default:
                        $wm_img_start_y = 5;
                        break;
                }
            }
        }

        return $this->watermarkImageProcess($wm_img_path, $wm_img_start_x, $wm_img_start_y);
    }// watermarkImage


    /**
     * Process watermark image to the main image.
     * 
     * @param string $wm_img_path Path to watermark image.
     * @param integer $wm_img_start_x Position to begin in x axis. The valus is integer or 'left', 'center', 'right'.
     * @param integer $wm_img_start_y Position to begin in x axis. The valus is integer or 'top', 'middle', 'bottom'.
     * @return boolean Return true on success, false on failed. Call to status_msg property to see the details on failure.
     */
    private function watermarkImageProcess($wm_img_path, $wm_img_start_x = 0, $wm_img_start_y = 0)
    {
        $this->setupWatermarkImageObject($wm_img_path);

        if ($this->isPreviousError()) {
            return false;
        }

        switch ($this->watermark_image_type) {
            case '1':
                // gif
            case '2':
                // jpg
            case '3':
                // png
                if ($this->source_image_frames > 1) {
                    // if source image is animated gif
                    $this->Imagick = $this->Imagick->coalesceImages();
                    if (is_object($this->Imagick)) {
                        $i = 1;
                        foreach ($this->Imagick as $Frame) {
                            $Frame->compositeImage($this->ImagickWatermark, \Imagick::COMPOSITE_DEFAULT, $wm_img_start_x, $wm_img_start_y);
                            $Frame->setImagePage(0, 0, 0, 0);
                            if ($i == 1) {
                                $this->ImagickFirstFrame = $Frame->getImage();
                            }
                            $i++;
                        }
                        unset($Frame, $i);
                    }
                } else {
                    $this->Imagick->compositeImage($this->ImagickWatermark, \Imagick::COMPOSITE_DEFAULT, $wm_img_start_x, $wm_img_start_y);
                    if ($this->source_image_type == '1') {
                        // if source image is gif, set image page to prevent sizing error.
                        $this->Imagick->setImagePage(0, 0, 0, 0);
                    }
                    $this->ImagickFirstFrame = null;
                }
                break;
            default:
                $this->status = false;
                $this->status_msg = 'Unable to set watermark from this kind of image.';
                return false;
        }

        if (is_object($this->ImagickWatermark)) {
            $this->ImagickWatermark->clear();
        }

        $this->destination_image_height = $this->Imagick->getImageHeight();
        $this->destination_image_width = $this->Imagick->getImageWidth();

        $this->source_image_height = $this->destination_image_height;
        $this->source_image_width = $this->destination_image_width;

        $this->status = true;
        $this->status_msg = null;
        return true;
    }// watermarkImageProcess


    /**
     * {@inheritDoc}
     */
    public function watermarkText($wm_txt_text, $wm_txt_font_path, $wm_txt_start_x = 0, $wm_txt_start_y = 0, $wm_txt_font_size = 10, $wm_txt_font_color = 'transwhitetext', $wm_txt_font_alpha = 60)
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

        if (!is_file($wm_txt_font_path)) {
            $this->status = false;
            $this->status_msg = 'Unable to load font file.';
            return false;
        }
        $wm_txt_font_path = realpath($wm_txt_font_path);

        // find text width and height
        // +10 will be -5 padding on watermark text area.
        $ImagickDraw = new \ImagickDraw();
        $ImagickDraw->setFont($wm_txt_font_path);
        $ImagickDraw->setFontSize($wm_txt_font_size);
        $ImagickDraw->setGravity(\Imagick::GRAVITY_NORTHWEST);
        // set new resolution for font due to it is smaller than GD if it was not set.
        $ImagickDraw->setresolution(96, 96);
        $type_space = $this->Imagick->queryFontMetrics($ImagickDraw, $wm_txt_text, false);
        if (is_array($type_space) && array_key_exists('textWidth', $type_space) && array_key_exists('textHeight', $type_space)) {
            $wm_txt_height = $type_space['textHeight'];
            $wm_txt_width = $type_space['textWidth'];
        }
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
                        $image_width = $this->Imagick->getImageWidth();
                        $watermark_width = $wm_txt_width;

                        $wm_txt_start_x = $this->calculateStartXOfCenter($watermark_width, $image_width);

                        unset($image_width, $watermark_width);
                        break;
                    case 'right':
                        $image_width = $this->Imagick->getImageWidth();
                        $wm_txt_start_x = intval($image_width - $wm_txt_width) - 5;// minus 5 because Imagick is different from GD.

                        unset($image_width);
                        break;
                    case 'left':
                    default:
                        $wm_txt_start_x = 10;// Imagick is different from GD, so increase it.
                        break;
                }
            }

            if (!is_numeric($wm_txt_start_y)) {
                switch (strtolower($wm_txt_start_y)) {
                    case 'middle':
                        $image_height = $this->Imagick->getImageHeight();
                        $watermark_height = $wm_txt_height;

                        $wm_txt_start_y = $this->calculateStartXOfCenter($watermark_height, $image_height);

                        unset($image_height, $watermark_height);
                        break;
                    case 'bottom':
                        $image_height = $this->Imagick->getImageHeight();
                        if ($image_height - ($wm_txt_height + 5) > '0') {
                            $wm_txt_start_y = intval($image_height - ($wm_txt_height + 5));
                        } else {
                            $wm_txt_start_y = intval($image_height - $wm_txt_height);
                        }
                        unset($image_height);
                        break;
                    case 'top':
                    default:
                        $wm_txt_start_y = 10;// Imagick is different from GD, so increase it.
                        break;
                }
            }
        }

        // begins watermark text --------------------------------------------------------------------------------------------
        // set color
        $black = new \ImagickPixel('black');
        $white = new \ImagickPixel('white');
        $transwhite = new \ImagickPixel('rgba(255, 255, 255, 0)');// set color transparent white
        $transwhitetext = new \ImagickPixel('rgba(255, 255, 255, '.$this->convertAlpha127ToRgba($wm_txt_font_alpha).')');
        if (!isset($$wm_txt_font_color)) {
            $wm_txt_font_color = 'transwhitetext';
        }

        // fill font color
        $ImagickDraw->setFillColor($$wm_txt_font_color);

        // write text on image
        if ($this->source_image_frames > 1) {
            // if source image is animated gif
            $this->Imagick = $this->Imagick->coalesceImages();
            if (is_object($this->Imagick)) {
                $i = 1;
                foreach ($this->Imagick as $Frame) {
                    $Frame->annotateImage($ImagickDraw, $wm_txt_start_x, $wm_txt_start_y, 0, $wm_txt_text);
                    $Frame->setImagePage(0, 0, 0, 0);
                    if ($i == 1) {
                        $this->ImagickFirstFrame = $Frame->getImage();
                    }
                    $i++;
                }
                unset($Frame, $i);
            }
        } else {
            $this->Imagick->annotateImage($ImagickDraw, $wm_txt_start_x, $wm_txt_start_y, 0, $wm_txt_text);
            if ($this->source_image_type == '1') {
                // if source image is gif, set image page to prevent sizing error.
                $this->Imagick->setImagePage(0, 0, 0, 0);
            }
            $this->ImagickFirstFrame = null;
        }
        // end watermark text -----------------------------------------------------------------------------------------------

        $ImagickDraw->clear();
        $black->destroy();
        $transwhite->destroy();
        $transwhitetext->destroy();
        $white->destroy();
        unset($black, $ImagickDraw, $transwhite, $transwhitetext, $white, $wm_txt_height, $wm_txt_width);

        $this->destination_image_height = $this->Imagick->getImageHeight();
        $this->destination_image_width = $this->Imagick->getImageWidth();

        $this->source_image_height = $this->destination_image_height;
        $this->source_image_width = $this->destination_image_width;

        $this->status = true;
        $this->status_msg = null;
        return true;
    }// watermarkText


}
