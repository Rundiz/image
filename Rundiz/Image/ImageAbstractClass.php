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


    protected function isClassSetup()
    {
        if ($this->source_image_path == null) {
            return false;
        }

        return true;
    }// isClassSetup


}
