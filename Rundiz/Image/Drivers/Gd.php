<?php
/**
 * PHP Image manipulation class.
 * 
 * @package Image
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 */


namespace Rundiz\Image\Drivers;

use Rundiz\Image\ImageAbstractClass;

/**
 * GD driver for image manipulation.
 *
 * @since 3.0
 */
class Gd extends ImageAbstractClass
{


    /**
     * @var mixed Image resource identifier
     */
    private $destination_image_object = null;
    /**
     * @var mixed Image resource identifier
     */
    private $source_image_object = null;

    /**
     * @var mixed Image resource identifier for watermark image.
     */
    private $watermark_image_object = null;
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
        return parent::__construct($source_image_path);
    }// __construct


    /**
     * Class de-constructor.
     */
    public function __destruct()
    {
        $this->clear();
    }// __destruct
	
	
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
        if ($this->destination_image_object != null && get_resource_type($this->destination_image_object) === 'gd') {
            imagedestroy($this->destination_image_object);
        }

        if ($this->source_image_object != null && get_resource_type($this->source_image_object) === 'gd') {
            imagedestroy($this->source_image_object);
        }

        if ($this->watermark_image_object != null && get_resource_type($this->watermark_image_object) === 'gd') {
            imagedestroy($this->watermark_image_object);
        }

        $this->destination_image_object = null;
        $this->source_image_object = null;
        $this->watermark_image_object = null;

        $this->watermark_image_height = null;
        $this->watermark_image_type = null;
        $this->watermark_image_width = null;

        $this->status = false;
        $this->status_msg = null;
        $this->destination_image_height = null;
        $this->destination_image_width = null;
        $this->last_modified_image_height = null;
        $this->last_modified_image_width = null;

        $this->buildSourceImageData($this->source_image_path);
    }// clear


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

        // calculate start x while $start_x was set as 'center'.
        if ($start_x === 'center') {
            $canvas_width = imagesx($this->source_image_object);
            $object_width = imagesx($this->destination_image_object);

            $start_x = $this->calculateStartXOfCenter($object_width, $canvas_width);

            unset($canvas_width, $object_width);
        } else {
            $start_x = intval($start_x);
        }

        // calculate start y while $start_y was set as 'middle'
        if ($start_y === 'middle') {
            $canvas_height = imagesy($this->source_image_object);
            $object_height = imagesy($this->destination_image_object);

            $start_y = $this->calculateStartXOfCenter($object_height, $canvas_height);

            unset($canvas_height, $object_height);
        } else {
            $start_y = intval($start_y);
        }

        // set color
        $black = imagecolorallocate($this->destination_image_object, 0, 0, 0);
        $white = imagecolorallocate($this->destination_image_object, 255, 255, 255);
        $transwhite = imagecolorallocatealpha($this->destination_image_object, 255, 255, 255, 127);// set color transparent white

        if ($fill != 'transparent' && $fill != 'white' && $fill != 'black') {
            $fill = 'transparent';
        }

        // begins crop
        if ($this->source_image_type === IMAGETYPE_GIF) {
            // gif
            if ($fill == 'transparent') {
                imagefill($this->destination_image_object, 0, 0, $transwhite);
                imagecolortransparent($this->destination_image_object, $transwhite);
            } else {
                imagefill($this->destination_image_object, 0, 0, $$fill);
            }

            imagecopy($this->destination_image_object, $this->source_image_object, 0, 0, $start_x, $start_y, $width, $height);

            // fill "again" in case that cropping image is larger than source image.
            if ($width > imagesx($this->source_image_object) || $height > imagesy($this->source_image_object)) {
                if ($fill == 'transparent') {
                    imagefill($this->destination_image_object, 0, 0, $transwhite);
                    imagecolortransparent($this->destination_image_object, $transwhite);
                } else {
                    imagefill($this->destination_image_object, 0, 0, $$fill);
                }
            }
        } elseif ($this->source_image_type === IMAGETYPE_JPEG) {
            // jpg
            imagecopy($this->destination_image_object, $this->source_image_object, 0, 0, $start_x, $start_y, $width, $height);

            if ($fill != 'transparent') {
                imagefill($this->destination_image_object, 0, 0, $$fill);
            }
        } elseif ($this->source_image_type === IMAGETYPE_PNG) {
            // png
            if ($fill == 'transparent') {
                imagefill($this->destination_image_object, 0, 0, $transwhite);
                imagecolortransparent($this->destination_image_object, $black);
                imagealphablending($this->destination_image_object, false);
                imagesavealpha($this->destination_image_object, true);
            } else {
                imagefill($this->destination_image_object, 0, 0, $$fill);
            }

            imagecopy($this->destination_image_object, $this->source_image_object, 0, 0, $start_x, $start_y, $width, $height);

            // fill "again" in case that cropping image is larger than source image.
            if ($width > imagesx($this->source_image_object) || $height > imagesy($this->source_image_object)) {
                if ($fill == 'transparent') {
                    imagefill($this->destination_image_object, 0, 0, $transwhite);
                    imagecolortransparent($this->destination_image_object, $black);
                    imagealphablending($this->destination_image_object, false);
                    imagesavealpha($this->destination_image_object, true);
                } else {
                    imagefill($this->destination_image_object, 0, 0, $$fill);
                }
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

        // clear unused variables
        if (get_resource_type($this->source_image_object) === 'gd') {
            imagedestroy($this->source_image_object);
            $this->source_image_object = null;
        }
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

        // get and set source (or last modified) image width and height
        $source_image_width = $this->source_image_width;
        if ($this->last_modified_image_width != null) {
            $source_image_width = $this->last_modified_image_width;
        }
        $source_image_height = $this->source_image_height;
        if ($this->last_modified_image_height != null) {
            $source_image_height = $this->last_modified_image_height;
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

        // begins resize
        if ($this->source_image_type === IMAGETYPE_GIF) {
            // gif
            $transwhite = imagecolorallocatealpha($this->destination_image_object, 255, 255, 255, 127);
            imagefill($this->destination_image_object, 0, 0, $transwhite);
            imagecolortransparent($this->destination_image_object, $transwhite);
            imagecopyresampled($this->destination_image_object, $this->source_image_object, 0, 0, 0, 0, $width, $height, $source_image_width, $source_image_height);
            unset($transwhite);
        } elseif ($this->source_image_type === IMAGETYPE_JPEG) {
            // jpg
            imagecopyresampled($this->destination_image_object, $this->source_image_object, 0, 0, 0, 0, $width, $height, $source_image_width, $source_image_height);
        } elseif ($this->source_image_type === IMAGETYPE_PNG) {
            // png
            imagealphablending($this->destination_image_object, false);
            imagesavealpha($this->destination_image_object, true);
            imagecopyresampled($this->destination_image_object, $this->source_image_object, 0, 0, 0, 0, $width, $height, $source_image_width, $source_image_height);
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

        // clear
        if (get_resource_type($this->source_image_object) === 'gd') {
            if ($this->source_image_object != $this->destination_image_object) {
                imagedestroy($this->source_image_object);
            }
            $this->source_image_object = null;
        }
        unset($source_image_height, $source_image_width);
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
                case IMAGETYPE_GIF:
                    // gif
                    // set source image width and height
                    $source_image_width = imagesx($this->source_image_object);
                    $source_image_height = imagesy($this->source_image_object);

                    $this->destination_image_object = imagecreatetruecolor($source_image_width, $source_image_height);
                    $transwhite = imagecolorallocatealpha($this->destination_image_object, 255, 255, 255, 127);
                    imagefill($this->destination_image_object, 0, 0, $transwhite);
                    imagecolortransparent($this->destination_image_object, $transwhite);
                    imagecopy($this->destination_image_object, $this->source_image_object, 0, 0, 0, 0, $source_image_width, $source_image_height);
                    $this->destination_image_object = imagerotate($this->destination_image_object, $degree, $transwhite);
                    unset($source_image_height, $source_image_width, $transwhite);
                    break;
                case IMAGETYPE_JPEG:
                    // jpg
                    $white = imagecolorallocate($this->source_image_object, 255, 255, 255);
                    $this->destination_image_object = imagerotate($this->source_image_object, $degree, $white);
                    unset($white);
                    break;
                case IMAGETYPE_PNG:
                    // png
                    $transwhite = imageColorAllocateAlpha($this->source_image_object, 0, 0, 0, 127);
                    $this->destination_image_object = imagerotate($this->source_image_object, $degree, $transwhite);
                    imagealphablending($this->destination_image_object, false);
                    imagesavealpha($this->destination_image_object, true);
                    unset($transwhite);
                    break;
                default:
                    $this->status = false;
                    $this->status_msg = 'Unable to rotate this kind of image.';
                    return false;
            }

            $this->destination_image_height = imagesy($this->destination_image_object);
            $this->destination_image_width = imagesx($this->destination_image_object);

            $this->source_image_height = $this->destination_image_height;
            $this->source_image_width = $this->destination_image_width;
        } else {
            // flip image
            if (version_compare(phpversion(), '5.5', '<')) {
                $this->status = false;
                $this->status_msg = 'Unable to flip image using PHP older than 5.5.';
                return false;
            }

            if ($degree == 'hor') {
                $mode = IMG_FLIP_HORIZONTAL;
            } elseif ($degree == 'vrt') {
                $mode = IMG_FLIP_VERTICAL;
            } else {
                $mode = IMG_FLIP_BOTH;
            }

            // flip image.
            imageflip($this->source_image_object, $mode);
            unset($mode);

            $this->destination_image_object = $this->source_image_object;
            $this->destination_image_height = imagesy($this->source_image_object);
            $this->destination_image_width = imagesx($this->source_image_object);

            $this->source_image_height = $this->destination_image_height;
            $this->source_image_width = $this->destination_image_width;
        }

        // clear
        if (get_resource_type($this->source_image_object) === 'gd') {
            if ($this->source_image_object != $this->destination_image_object) {
                imagedestroy($this->source_image_object);
            }
            $this->source_image_object = null;
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

        // in case that it was called new Gd object and then save without any modification.
        if ($this->destination_image_object == null && $this->source_image_object == null) {
            $this->resizeNoRatio($this->source_image_width, $this->source_image_height);
        }

        // check previous step contain errors?
        if ($this->isPreviousError() === true) {
            return false;
        }

        // save to file. each image types use different ways to save.
        if ($check_file_ext == 'gif') {
            if ($this->source_image_type === IMAGETYPE_PNG) {
                // source image is png file. convert transparency png to white before save.
                $temp_image_object = imagecreatetruecolor($this->destination_image_width, $this->destination_image_height);
                $white = imagecolorallocate($temp_image_object, 255, 255, 255);
                imagefill($temp_image_object, 0, 0, $white);
                imagecopy($temp_image_object, $this->destination_image_object, 0, 0, 0, 0, $this->destination_image_width, $this->destination_image_height);
                $this->destination_image_object = $temp_image_object;
                unset($temp_image_object, $white);
            }

            $save_result = imagegif($this->destination_image_object, $file_name);
        } elseif ($check_file_ext == 'jpg') {
            if ($this->source_image_type === IMAGETYPE_PNG || $this->source_image_type === IMAGETYPE_GIF) {
                // source image is png or gif file. convert transparency png to white before save.
                $temp_image_object = imagecreatetruecolor($this->destination_image_width, $this->destination_image_height);
                $white = imagecolorallocate($temp_image_object, 255, 255, 255);
                imagefill($temp_image_object, 0, 0, $white);
                imagecopy($temp_image_object, $this->destination_image_object, 0, 0, 0, 0, $this->destination_image_width, $this->destination_image_height);
                $this->destination_image_object = $temp_image_object;
                unset($temp_image_object, $white);
            }

            $this->jpg_quality = intval($this->jpg_quality);
            if ($this->jpg_quality < 0 || $this->jpg_quality > 100) {
                $this->jpg_quality = 100;
            }

            $save_result = imagejpeg($this->destination_image_object, $file_name, $this->jpg_quality);
        } elseif ($check_file_ext == 'png') {
            if ($this->source_image_type === IMAGETYPE_GIF) {
                // source image is gif file. convert transparency gif to white before save.
                // source transparent png to gif have no problem but source transparent gif to png it always left transparency. it must be filled.
                $temp_image_object = imagecreatetruecolor($this->destination_image_width, $this->destination_image_height);
                $white = imagecolorallocate($temp_image_object, 255, 255, 255);
                imagefill($temp_image_object, 0, 0, $white);
                imagecopy($temp_image_object, $this->destination_image_object, 0, 0, 0, 0, $this->destination_image_width, $this->destination_image_height);
                $this->destination_image_object = $temp_image_object;
                unset($temp_image_object, $white);
            }

            $this->png_quality = intval($this->png_quality);
            if ($this->png_quality < 0 || $this->png_quality > 9) {
                $this->png_quality = 0;
            }

            $save_result = imagepng($this->destination_image_object, $file_name, $this->png_quality);
        } else {
            $this->status = false;
            $this->status_msg = sprintf('Unable to save this kind of image. (%s)', $check_file_ext);
            return false;
        }

        // clear
        unset($check_file_ext, $file_ext);

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
     * Setup destination image object that must have size in width and height for use with resize, crop.
     * 
     * @param integer $width Destination image object width.
     * @param integer $height Destination image object height.
     * @return boolean Return true on success, false on failed. Call to status_msg property to see the details on failure.
     */
    private function setupDestinationImageObjectWithSize($width, $height)
    {
        if (false === $this->isClassSetup()) {
            return false;
        }

        if ($this->isPreviousError() === true) {
            return false;
        }

        if ($this->destination_image_object == null || get_resource_type($this->destination_image_object) !== 'gd') {
            $this->destination_image_object = imagecreatetruecolor($width, $height);
        }

        // come to this means destination image object is already set.
        $this->status = true;
        $this->status_msg = null;
        return true;
    }// setupDestinationImageObjectWithSize


    /**
     * Setup source image object.
     * After calling this the source_image_object will get new image resource by chaining from previous destination or from image file.
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

        if ($this->source_image_object == null || get_resource_type($this->source_image_object) !== 'gd') {
            if ($this->destination_image_object != null && get_resource_type($this->destination_image_object) === 'gd') {
                $this->source_image_object = $this->destination_image_object;
                $this->destination_image_object = null;

                $this->status = true;
                $this->status_msg = null;
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
                }

                if ($this->source_image_object != null) {
                    $this->status = true;
                    $this->status_msg = null;
                    return true;
                } else {
                    $this->status = false;
                    $this->status_msg = 'Unable to set source from this kind of image.';
                    return false;
                }
            }
        }

        // come to this means source image object is already set.
        $this->status = true;
        $this->status_msg = null;
        return true;
    }// setupSourceImageObject


    /**
     * Setup watermark image object.
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

        list($wm_width, $wm_height, $wm_type) = getimagesize($wm_img_path);

        if ($wm_height == null || $wm_width == null || $wm_type == null) {
            $this->status = false;
            $this->status_msg = 'Watermark is not an image.';
            return false;
        }

        if ($this->watermark_image_object != null && !is_bool($this->watermark_image_object) && get_resource_type($this->watermark_image_object) === 'gd') {
            imagedestroy($this->watermark_image_object);
        }

        switch ($wm_type) {
            case '1':
                $this->watermark_image_object = imagecreatefromgif($wm_img_path);
                // add alpha to support transparency gif
                imagesavealpha($this->watermark_image_object, true);
                break;
            case '2':
                $this->watermark_image_object = imagecreatefromjpeg($wm_img_path);
                break;
            case '3':
                $this->watermark_image_object = imagecreatefrompng($wm_img_path);
                // add alpha, alpha blending to support transparency png
                imagealphablending($this->watermark_image_object, false);
                imagesavealpha($this->watermark_image_object, true);
                break;
            default:
                $this->status = false;
                $this->status_msg = 'Unable to set watermark from this kind of image.';
                return false;
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

        // in case that it was called new Gd object and then save without any modification.
        if ($this->destination_image_object == null && $this->source_image_object == null) {
            $this->resizeNoRatio($this->source_image_width, $this->source_image_height);
        }

        // check previous step contain errors?
        if ($this->isPreviousError() === true) {
            return false;
        }

        // show image to browser.
        if ($check_file_ext == 'gif') {
            if ($this->source_image_type === IMAGETYPE_PNG) {
                // source image is png file. convert transparency png to white before save.
                $temp_image_object = imagecreatetruecolor($this->destination_image_width, $this->destination_image_height);
                $white = imagecolorallocate($temp_image_object, 255, 255, 255);
                imagefill($temp_image_object, 0, 0, $white);
                imagecopy($temp_image_object, $this->destination_image_object, 0, 0, 0, 0, $this->destination_image_width, $this->destination_image_height);
                $this->destination_image_object = $temp_image_object;
                unset($white);
            }

            $show_result = imagegif($this->destination_image_object);

            if (isset($temp_image_object) && $temp_image_object != null && get_resource_type($temp_image_object) === 'gd') {
                imagedestroy($temp_image_object);
                unset($temp_image_object);
            }
        } elseif ($check_file_ext == 'jpg') {
            if ($this->source_image_type === IMAGETYPE_PNG || $this->source_image_type === IMAGETYPE_GIF) {
                // source image is png or gif file. convert transparency png to white before save.
                $temp_image_object = imagecreatetruecolor($this->destination_image_width, $this->destination_image_height);
                $white = imagecolorallocate($temp_image_object, 255, 255, 255);
                imagefill($temp_image_object, 0, 0, $white);
                imagecopy($temp_image_object, $this->destination_image_object, 0, 0, 0, 0, $this->destination_image_width, $this->destination_image_height);
                $this->destination_image_object = $temp_image_object;
                unset($white);
            }

            $this->jpg_quality = intval($this->jpg_quality);
            if ($this->jpg_quality < 0 || $this->jpg_quality > 100) {
                $this->jpg_quality = 100;
            }

            $show_result = imagejpeg($this->destination_image_object, null, $this->jpg_quality);

            if (isset($temp_image_object) && $temp_image_object != null && get_resource_type($temp_image_object) === 'gd') {
                imagedestroy($temp_image_object);
                unset($temp_image_object);
            }
        } elseif ($check_file_ext == 'png') {
            if ($this->source_image_type === IMAGETYPE_GIF) {
                // source image is gif file. convert transparency gif to white before save.
                // source transparent png to gif have no problem but source transparent gif to png it always left transparency. it must be filled.
                $temp_image_object = imagecreatetruecolor($this->destination_image_width, $this->destination_image_height);
                $white = imagecolorallocate($temp_image_object, 255, 255, 255);
                imagefill($temp_image_object, 0, 0, $white);
                imagecopy($temp_image_object, $this->destination_image_object, 0, 0, 0, 0, $this->destination_image_width, $this->destination_image_height);
                $this->destination_image_object = $temp_image_object;
                unset($temp_image_object, $white);
            }

            $this->png_quality = intval($this->png_quality);
            if ($this->png_quality < 0 || $this->png_quality > 9) {
                $this->png_quality = 0;
            }

            $show_result = imagepng($this->destination_image_object, null, $this->png_quality);
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
            return true;
        } else {
            $this->status = false;
            $this->status_msg = 'Failed to show the image.';
            return false;
        }
    }// show


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
                        $image_width = imagesx($this->source_image_object);
                        $watermark_width = imagesx($this->watermark_image_object);

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
                        $image_height = imagesy($this->source_image_object);
                        $watermark_height = imagesy($this->watermark_image_object);

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
                imagecopy($this->source_image_object, $this->watermark_image_object, $wm_img_start_x, $wm_img_start_y, 0, 0, $this->watermark_image_width, $this->watermark_image_height);
                break;
            case '3':
                // png
                if ($this->source_image_type === IMAGETYPE_GIF) {
                    // source image is gif (which maybe transparent) and watermark image is png. so, this cannot just use imagecopy() function.
                    // see more at http://stackoverflow.com/questions/4437557/using-gd-in-php-how-can-i-make-a-transparent-png-watermark-on-png-and-gif-files
                    $cut_resource_object = imagecreatetruecolor($this->watermark_image_width, $this->watermark_image_height);
                    imagecopy($cut_resource_object, $this->source_image_object, 0, 0, $wm_img_start_x, $wm_img_start_y, $this->watermark_image_width, $this->watermark_image_height);
                    imagecopy($cut_resource_object, $this->watermark_image_object, 0, 0, 0, 0, $this->watermark_image_width, $this->watermark_image_height);
                    imagecopymerge($this->source_image_object, $cut_resource_object, $wm_img_start_x, $wm_img_start_y, 0, 0, $this->watermark_image_width, $this->watermark_image_height, 100);
                    imagedestroy($cut_resource_object);
                } else {
                    imagealphablending($this->source_image_object, true);// add this for transparent watermark thru image.
                    imagecopy($this->source_image_object, $this->watermark_image_object, $wm_img_start_x, $wm_img_start_y, 0, 0, $this->watermark_image_width, $this->watermark_image_height);
                }
                break;
            default:
                $this->status = false;
                $this->status_msg = 'Unable to set watermark from this kind of image.';
                return false;
        }

        if ($this->watermark_image_object != null && !is_bool($this->watermark_image_object) && get_resource_type($this->watermark_image_object) === 'gd') {
            imagedestroy($this->watermark_image_object);
        }
        if ($this->destination_image_object == null) {
            $this->destination_image_object = $this->source_image_object;
            $this->source_image_object = null;
        }

        $this->destination_image_height = imagesy($this->destination_image_object);
        $this->destination_image_width = imagesx($this->destination_image_object);

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

        // find text width and height
        // @link copy from here http://stackoverflow.com/questions/11696920/calculating-text-width-with-php-gd
        // +10 will be -5 padding on watermark text area
        $type_space = imagettfbbox($wm_txt_font_size, 0, $wm_txt_font_path, $wm_txt_text);
        $wm_txt_height = abs($type_space[5] - $type_space[1]) + 10;
        $wm_txt_width = abs($type_space[4] - $type_space[0]) + 10;
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
                        $image_width = imagesx($this->source_image_object);
                        $watermark_width = $wm_txt_width;

                        $wm_txt_start_x = $this->calculateStartXOfCenter($watermark_width, $image_width);

                        unset($image_width, $watermark_width);
                        break;
                    case 'right':
                        $image_width = imagesx($this->source_image_object);
                        $wm_txt_start_x = intval($image_width - $wm_txt_width);

                        unset($image_width);
                        break;
                    case 'left':
                    default:
                        $wm_txt_start_x = 5;
                        break;
                }
            }

            if (!is_numeric($wm_txt_start_y)) {
                switch (strtolower($wm_txt_start_y)) {
                    case 'middle':
                        $image_height = imagesy($this->source_image_object);
                        $watermark_height = $wm_txt_height;

                        $wm_txt_start_y = $this->calculateStartXOfCenter($watermark_height, $image_height);

                        unset($image_height, $watermark_height);
                        break;
                    case 'bottom':
                        $image_height = imagesy($this->source_image_object);
                        if ($image_height - ($wm_txt_height + 5) > '0') {
                            $wm_txt_start_y = intval($image_height - ($wm_txt_height + 5));
                        } else {
                            $wm_txt_start_y = intval($image_height - $wm_txt_height);
                        }
                        unset($image_height);
                        break;
                    case 'top':
                    default:
                        $wm_txt_start_y = 5;
                        break;
                }
            }
        }

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
        $transwhite = imagecolorallocatealpha($wm_txt_object, 255, 255, 255, 127);// set color transparent white
        $transwhitetext = imagecolorallocatealpha($wm_txt_object, 255, 255, 255, $wm_txt_font_alpha);
        if (!isset($$wm_txt_font_color)) {
            $wm_txt_font_color = 'transwhitetext';
        }

        // set text
        imagefill($wm_txt_object, 0, 0, $transwhite);
        // y coords below must -5 to allow something like p, g show full size
        imagettftext($wm_txt_object, $wm_txt_font_size, 0, 5, $wm_txt_height-5, $$wm_txt_font_color, $wm_txt_font_path, $wm_txt_text);

        // copy text to image
        switch ($this->source_image_type) {
            case IMAGETYPE_GIF:
                // gif
                $cut_resource_object = imagecreatetruecolor($wm_txt_width, $wm_txt_height);
                imagecopy($cut_resource_object, $this->source_image_object, 0, 0, $wm_txt_start_x, $wm_txt_start_y, $wm_txt_width, $wm_txt_height);
                imagecopy($cut_resource_object, $wm_txt_object, 0, 0, 0, 0, $wm_txt_width, $wm_txt_height);
                imagecopymerge($this->source_image_object, $cut_resource_object, $wm_txt_start_x, $wm_txt_start_y, 0, 0, $wm_txt_width, $wm_txt_height, 100);
                imagedestroy($cut_resource_object);
                break;
            case IMAGETYPE_PNG:
                // png
                imagealphablending($this->source_image_object, true);
            case IMAGETYPE_JPEG:
                // jpg
            default:
                imagecopy($this->source_image_object, $wm_txt_object, $wm_txt_start_x, $wm_txt_start_y, 0, 0, $wm_txt_width, $wm_txt_height);
                break;
        }
        // end watermark text -----------------------------------------------------------------------------------------------

        imagedestroy($wm_txt_object);
        unset($black, $transwhite, $transwhitetext, $white, $wm_txt_height, $wm_txt_width);

        if ($this->destination_image_object == null) {
            $this->destination_image_object = $this->source_image_object;
            $this->source_image_object = null;
        }

        $this->destination_image_height = imagesy($this->destination_image_object);
        $this->destination_image_width = imagesx($this->destination_image_object);

        $this->source_image_height = $this->destination_image_height;
        $this->source_image_width = $this->destination_image_width;

        $this->status = true;
        $this->status_msg = null;
        return true;
    }// watermarkText


}
