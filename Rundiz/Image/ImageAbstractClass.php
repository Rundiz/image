<?php
/**
 * PHP Image manipulation class.
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 */


namespace Rundiz\Image;

use Rundiz\Image\ImageInterface;

/**
 * Abstract class of Image class.
 *
 * @since 3.0
 */
abstract class ImageAbstractClass implements ImageInterface
{


    /**
     * Allow to set resize larger than source image.
     * @var boolean Set to true to allow, false to disallow.
     */
    public $allow_resize_larger = false;
    /**
     * JPEG quality.
     * @var integer Quality from 0 (worst quality, smallest file) to 100 (best quality, biggest file).
     */
    public $jpg_quality = 100;
    /**
     * PNG quality.
     * @var integer Compression level from 0 (no compression) to 9. 
     */
    public $png_quality = 0;
    /**
     * Master dimension.
     * @var string Master dimension value is 'auto', 'width', or 'height'.
     */
    public $master_dim = 'auto';
    /**
     * Contain status of action methods.
     * @var boolean Return false if there is something error.
     */
    public $status = false;
    /**
     * Contain status error message of action methods.
     * @var string Return error message.
     */
    public $status_msg = null;

    // Most of the properties below are unable to set or access directly from outside this class. --------------------
    /**
     * @var string Path to source image file.
     */
    protected $source_image_path;
    /**
     * @var integer Source image width. 
     */
    protected $source_image_width;
    /**
     * @var integer Source image height.
     */
    protected $source_image_height;
    /**
     * @var string Source image type. See more at http://php.net/manual/en/function.getimagesize.php The numbers of these extensions are: 1=gif, 2=jpg, 3=png
     */
    protected $source_image_type;
    /**
     * @var string Source image mime type. See more at http://php.net/manual/en/function.getimagesize.php
     */
    protected $source_image_mime;
    /**
     * @var string Source image file extension.
     */
    protected $source_image_ext;
    /**
     * @var array Source image data from getimagesize() function. See more at http://php.net/manual/en/function.getimagesize.php
     */
    public $source_image_data;

    /**
     * @var integer Last modified image width
     */
    protected $last_modified_image_width;
    /**
     * @var integer Last modified image height
     */
    protected $last_modified_image_height;

    /**
     * @var integer Destination image width.
     */
    protected $destination_image_width;
    /**
     * @var integer Destination image height.
     */
    protected $destination_image_height;


    /**
     * Class constructor.
     * 
     * @param string $source_image_path Path to source image file.
     * @return boolean Return true on success, false on failed. Call to status_msg property to see the details on failure.
     */
    public function __construct($source_image_path)
    {
        return $this->buildSourceImageData($source_image_path);
    }// __construct


    /**
     * Build source image data
     * 
     * @param string $source_image_path Path to source image file.
     * @return boolean Return true on success, false on failed. Call to status_msg property to see the details on failure.
     */
    protected function buildSourceImageData($source_image_path)
    {
        if (is_file($source_image_path)) {
            $source_image_path = realpath($source_image_path);
            $image_data = getimagesize($source_image_path);

            if (false !== $image_data && is_array($image_data) && !empty($image_data)) {
                $this->source_image_path = $source_image_path;
                $this->source_image_width = $image_data[0];
                $this->source_image_height = $image_data[1];
                $this->source_image_type = $image_data[2];
                $this->source_image_mime = $image_data['mime'];
                $this->source_image_ext = str_ireplace('jpeg', 'jpg', image_type_to_extension($image_data[2]));
                $this->source_image_data = $image_data;
                unset($image_data);

                $this->status = true;
                $this->status_msg = null;
                return true;
            } else {
                unset($image_data);

                $this->status = false;
                $this->status_msg = 'Unable to get image data. This file maybe a fake image.';
                return false;
            }
        } else {
            $this->status = false;
            $this->status_msg = 'Source image is not exists.';
            return false;
        }
    }// buildSourceImageData


    /**
     * Calculate image size by aspect ratio.
     * 
     * @param integer $width New width set to calculate.
     * @param integer $height New height set to calculate.
     * @return array Return array with 'height' and 'width' in array key and the values are calculated sizes.
     */
    protected function calculateImageSizeRatio($width, $height)
    {
        // convert width, height to integer
        $width = intval($width);
        $height = intval($height);

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

        $source_image_orientation = $this->getSourceImageOrientation();
        // find height and width by aspect ratio.
        $find_h = round(($source_image_height/$source_image_width)*$width);
        $find_w = round(($source_image_width/$source_image_height)*$height);

        $this->verifyMasterDimension();

        switch ($this->master_dim) {
            case 'width':
                $new_width = $width;
                $new_height = $find_h;

                // if not allow resize larger.
                if ($this->allow_resize_larger == false) {
                    // if new width larger than source image width
                    if ($width > $source_image_width) {
                        $new_width = $source_image_width;
                        $new_height = $source_image_height;
                    }
                }
                break;
            case 'height':
                $new_width = $find_w;
                $new_height = $height;

                // if not allow resize larger.
                if ($this->allow_resize_larger == false) {
                    // if new height is larger than source image height
                    if ($height > $source_image_height) {
                        $new_width = $source_image_width;
                        $new_height = $source_image_height;
                    }
                }
                break;
            case 'auto':
            default:
                // master dimension auto.
                switch ($source_image_orientation) {
                    case 'P':
                        // image orientation portrait
                        $new_width = $find_w;
                        $new_height = $height;

                        // if not allow resize larger
                        if ($this->allow_resize_larger == false) {
                            // determine new image size must not larger than source image size.
                            if ($height > $source_image_height && $width <= $source_image_width) {
                                // if new height larger than source image height and width smaller or equal to source image width
                                $new_width = $width;
                                $new_height = $find_h;
                            } else {
                                if ($height > $source_image_height) {
                                    $new_width = $source_image_width;
                                    $new_height = $source_image_height;
                                }
                            }
                        }
                        break;
                    case 'L':
                    // image orientation landscape
                    case 'S':
                    // image orientation square
                    default:
                        // image orientation landscape and square
                        $new_width = $width;
                        $new_height = $find_h;

                        // if not allow resize larger
                        if ($this->allow_resize_larger == false) {
                            // determine new image size must not larger than source image size.
                            if ($width > $source_image_width && $height <= $source_image_height) {
                                // if new width larger than source image width and height smaller or equal to source image height
                                $new_width = $find_w;
                                $new_height = $height;
                            } else {
                                if ($width > $source_image_width) {
                                    $new_width = $source_image_width;
                                    $new_height = $source_image_height;
                                }
                            }
                        }
                        break;
                }
                break;
        }// endswitch;

        unset($find_h, $find_w, $source_image_height, $source_image_orientation, $source_image_width);
        return [
            'height' => $new_height, 
            'width' => $new_width
        ];
    }// calculateImageSizeRatio


    /**
     * Get source image orientation.<br>
     * This method called by calculateImageSizeRatio().
     * 
     * @return string Return S for square, L for landscape, P for portrait.
     */
    protected function getSourceImageOrientation()
    {
        if ($this->source_image_height == $this->source_image_width) {
            // square image
            return 'S';
        } elseif ($this->source_image_height < $this->source_image_width) {
            // landscape image
            return 'L';
        } else {
            // portrait image
            return 'P';
        }
    }// getSourceImageOrientation


    /**
     * Verify that is class setup properly.
     * If image source was not found then it will not setup properly.
     * 
     * @return boolean Return true on success, return false on failed.
     */
    protected function isClassSetup()
    {
        if ($this->source_image_path == null) {
            return false;
        }

        return true;
    }// isClassSetup


    /**
     * Verify master dimension value must be correctly.<br>
     * This method called by calculateImageSizeRatio().
     */
    protected function verifyMasterDimension() 
    {
       $this->master_dim = strtolower($this->master_dim);

       if ($this->master_dim != 'auto' && $this->master_dim != 'width' && $this->master_dim != 'height') {
           $this->master_dim = 'auto';
       }
    }// verifyMasterDimension


}
