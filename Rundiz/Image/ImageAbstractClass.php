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


    use Traits\CalculationTrait;


    /**
     * Allow to set resize larger than source image.
     * @var bool Set to `true` to allow, `false` to disallow. Default is `false`.
     */
    public $allow_resize_larger = false;
    /**
     * JPEG quality.
     * @var int Quality from 0 (worst quality, smallest file) to 100 (best quality, biggest file). Default is 100.
     */
    public $jpg_quality = 100;
    /**
     * PNG quality.
     * @var int Compression level from 0 (no compression) to 9.  Default is 0.
     */
    public $png_quality = 0;
    /**
     * Master dimension.
     * @var string Master dimension value is 'auto', 'width', or 'height'. Default is 'auto'.
     */
    public $master_dim = 'auto';
    /**
     * Contain status of action methods.
     * @var bool Return `false` if there is something error.
     */
    public $status = false;
    /**
     * Contain status error message of action methods.
     * @var string Return error message. Default is `null`.
     */
    public $status_msg = null;
    /**
     * Add bottom padding to watermark text to let characters that long to the bottom can be displayed. Example p, g, ฤ, ฎ, etc.<br>
     * This bottom padding should be different for each font size.
     * @var int Set the number of pixels to add to watermark text at the bottom. Default is 5, previous version was 5.
     */
    public $wmTextBottomPadding = 5;

    // Most of the properties below are unable to set or access directly from outside this class. --------------------
    /**
     * @var string Path to source image file.
     */
    protected $source_image_path;
    /**
     * @var int Source image width. 
     */
    protected $source_image_width;
    /**
     * @var int Source image height.
     */
    protected $source_image_height;
    /**
     * @var string Source image type. The numbers of these extensions are: 1=gif, 2=jpg, 3=png, 18=webp
     */
    protected $source_image_type;
    /**
     * @var string Source image mime type. Example `image/jpeg`.
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
     * @var int Last modified image width
     */
    protected $last_modified_image_width;
    /**
     * @var int Last modified image height
     */
    protected $last_modified_image_height;

    /**
     * @var int Destination image width.
     */
    protected $destination_image_width;
    /**
     * @var int Destination image height.
     */
    protected $destination_image_height;


    /**
     * Class constructor.
     * 
     * @param string $source_image_path Path to source image file.
     * @return bool Return true on success, false on failed. Call to status_msg property to see the details on failure.
     */
    public function __construct($source_image_path)
    {
        return $this->buildSourceImageData($source_image_path);
    }// __construct


    /**
     * Build source image data
     * 
     * @param string $source_image_path Path to source image file.
     * @return bool Return true on success, false on failed. Call to status_msg property to see the details on failure.
     */
    protected function buildSourceImageData($source_image_path)
    {
        if (is_file($source_image_path)) {
            $source_image_path = realpath($source_image_path);
            $image_data = $this->getImageFileData($source_image_path);

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
     * Get image file data such as width, height, mime type.
     * 
     * This will be use `getimagesize()` function if supported, use GD functions as backup.
     * 
     * @since 3.1.0
     * @param string $imagePath Full path to image file.
     * @return array|false Return array:<br>
     *              index 0 Image width.<br>
     *              index 1 Image height.<br>
     *              index 2 Image type constant. See more at https://www.php.net/manual/en/image.constants.php <br>
     *              `mime` key is mime type.<br> 
     *              `ext` key is file extension with dot (.ext).<br>
     *              Return `false` on failure.
     */
    protected function getImageFileData($imagePath)
    {
        if (is_file($imagePath)) {
            $imagePath = realpath($imagePath);
            if (
                stripos($imagePath, '.webp') !== false && 
                version_compare(PHP_VERSION, '7.1.0', '<') &&
                function_exists('imagecreatefromwebp')
            ) {
                // if it is .webp and current PHP version is not supported and webp feature for GD is enabled.
                try {
                    $WebP = new Extensions\WebP();
                    $webpInfo = $WebP->webPInfo($imagePath);
                    if (is_array($webpInfo) && isset($webpInfo['Animation']) && $webpInfo['Animation'] === false) {
                        // if not animated webp.
                        $output = [];

                        // use gd to get width, height.
                        $GD = imagecreatefromwebp($imagePath);
                        if (false !== $GD) {
                            $output[0] = imagesx($GD);
                            $output[1] = imagesy($GD);
                            if (!defined('IMAGETYPE_WEBP')) {
                                define('IMAGETYPE_WEBP', 18);
                            }
                            $output[2] = IMAGETYPE_WEBP;
                            $output['mime'] = 'image/webp';
                            $output['ext'] = '.webp';
                            unset($GD);
                            return $output;
                        }
                        unset($GD, $output);
                    }
                    unset($WebP, $webpInfo);
                } catch (\Exception $ex) {
                    // failed.
                }
            } else {
                // if generic image.
                $imgResult = getimagesize($imagePath);
                if (
                        is_array($imgResult) &&
                        array_key_exists(0, $imgResult) &&
                        array_key_exists(1, $imgResult) &&
                        array_key_exists(2, $imgResult) &&
                        array_key_exists('mime', $imgResult)
                ) {
                    // if image was supported and it really is an image, these keys must exists.
                    $imgResult['ext'] = image_type_to_extension($imgResult[2]);
                    return $imgResult;
                }
                unset($imgResult);
            }
        }

        return false;
    }// getImageFileData


    /**
     * {@inheritDoc}
     */
    public function getImageSize()
    {
        return [
            'height' => $this->source_image_height,
            'width' => $this->source_image_width,
        ];
    }// getImageSize


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
     * @return bool Return true on success, return false on failed.
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
