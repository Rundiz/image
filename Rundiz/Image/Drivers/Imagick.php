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
     * @var integer Last modified image width
     */
    private $last_modified_image_width;
    /**
     * @var integer Last modified image height
     */
    private $last_modified_image_height;

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
                        if ($i == 1 && $this->ImagickFirstFrame == null) {
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
                        if ($i == 1 && $this->ImagickFirstFrame == null) {
                            $this->ImagickFirstFrame = $Frame->getImage();
                        }
                        $i++;
                    }
                    unset($Frame, $i);
                }
            } else {
                $this->Imagick->resizeImage($width, $height, $this->imagick_filter, 1);
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
    public function rotate($degree = '90')
    {
        if (false === $this->isClassSetup()) {
            return false;
        }

        // check degree
        $allowed_degree = array(0, 90, 180, 270, 'hor', 'vrt', 'horvrt');
        if (!in_array($degree, $allowed_degree)) {
            $degree = 90;
        }
        if (is_numeric($degree)) {
            $degree = intval($degree);
        }
        unset($allowed_degree);

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
                                if ($i == 1 && $this->ImagickFirstFrame == null) {
                                    $this->ImagickFirstFrame = $Frame->getImage();
                                }
                                $i++;
                            }
                            unset($Frame, $i);
                        }
                    } else {
                        $this->Imagick->rotateImage(new \ImagickPixel('rgba(255, 255, 255, 0)'), $this->calculateCounterClockwise($degree));
                        $this->ImagickFirstFrame = null;
                    }
                    break;
                case '2':
                    // jpg
                    // @todo continue.
                    break;
                case '3':
                    // png
                    
                    break;
                default:
                    $this->status = false;
                    $this->status_msg = 'Unable to rotate this kind of image.';
                    return false;
            }

            $this->destination_image_height = $this->Imagick->getImageHeight();
            $this->destination_image_width = $this->Imagick->getImageWidth();

            $this->source_image_height = $this->destination_image_height;
            $this->source_image_width = $this->destination_image_width;
        } else {
            
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

                $save_result = $this->Imagick->writeImage($real_file_name);
            }
        } elseif ($check_file_ext == 'jpg') {
            if ($this->source_image_type == '1') {
                // source file is gif
                // covnert from transparent to white before save
                $this->Imagick->setImageBackgroundColor('white');// convert from transparent to white. for GIF source
                $this->Imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);// convert from transparent to white. for GIF source
                $this->Imagick = $this->Imagick->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);

                if ($this->source_image_frames > 1 && is_object($this->ImagickFirstFrame)) {
                    // if source image is animated gif and save to non-animated gif, get the first frame.
                    $this->Imagick->clear();
                    $this->Imagick = $this->ImagickFirstFrame;
                    $this->ImagickFirstFrame = null;
                }
            } elseif ($this->source_image_type == '3') {
                // source file is png
                // convert from transparent to white before save
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
                // covnert from transparent to white before save
                $this->Imagick->setImageBackgroundColor('white');// convert from transparent to white. for GIF source
                $this->Imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);// convert from transparent to white. for GIF source
                $this->Imagick = $this->Imagick->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);

                if ($this->source_image_frames > 1 && is_object($this->ImagickFirstFrame)) {
                    // if source image is animated gif and save to non-animated gif, get the first frame.
                    $this->Imagick->clear();
                    $this->Imagick = $this->ImagickFirstFrame;
                    $this->ImagickFirstFrame = null;
                }
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
     * {@inheritDoc}
     */
    public function show($file_ext = '')
    {
        
    }// show


    /**
     * {@inheritDoc}
     */
    public function watermarkImage($wm_img_path, $wm_img_start_x = 0, $wm_img_start_y = 0)
    {
        
    }// watermarkImage


    /**
     * {@inheritDoc}
     */
    public function watermarkText($wm_txt_text, $wm_txt_font_path, $wm_txt_start_x = 0, $wm_txt_start_y = 0, $wm_txt_font_size = 10, $wm_txt_font_color = 'transwhitetext', $wm_txt_font_alpha = 60)
    {
        
    }// watermarkText


}
