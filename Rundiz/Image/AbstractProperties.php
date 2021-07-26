<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image;


/**
 * Abstract properties class.
 * 
 * For use properties between classes without implement interface.
 * 
 * @since 3.1.0
 * @property-read int $destination_image_height
 * @property-read int $destination_image_width
 * @property-read string $source_image_ext
 * @property-read int $source_image_height
 * @property-read string $source_image_mime
 * @property-read string $source_image_path
 * @property-read string $source_image_type
 * @property-read int $source_image_width
 * @property-read int $last_modified_image_height
 * @property-read int $last_modified_image_width
 * @property-read int $watermark_image_height
 * @property-read int $watermark_image_width
 * @property-read string $watermark_image_type
 */
abstract class AbstractProperties
{


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
    /**
     * Watermark text bounding box vertical (Y) padding.
     * @var int Set the number of watermark text bounding box padding. Default is 0.
     */
    public $wmTextBoundingBoxYPadding = 0;

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
     * @var string Watermark image type. The numbers of these extensions are: 1=gif, 2=jpg, 3=png, 18=webp or image type constant.<br>
     * See more at https://www.php.net/manual/en/image.constants.php
     */
    protected $watermark_image_type;
    /**
     * @var int Watermark image width
     */
    protected $watermark_image_width;
    /**
     * @var int Watermark image height
     */
    protected $watermark_image_height;


}
