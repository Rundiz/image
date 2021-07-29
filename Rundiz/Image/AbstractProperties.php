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


    // setting properties. ----------------------------------------------------------------------------------------------------
    /**
     * Allow to set resize larger than source image.
     * @var bool Set to `true` to allow, `false` to disallow. Default is `false`.
     */
    public $allow_resize_larger = false;
    /**
     * JPEG & WebP quality.
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


    // result properties. -----------------------------------------------------------------------------------------------------
    /**
     * Contain status of action methods.
     * @var bool Return `false` if there is something error.
     */
    public $status = false;
    /**
     * Contain status code refer from class constants.
     * @var int Return status code. See class constants. Default is `null`.
     */
    public $statusCode = null;
    /**
     * Contain status error message of action methods.
     * @var string Return error message. Default is `null`.
     */
    public $status_msg = null;


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


    /**
     * Not support alpha transparency WebP for current PHP version.
     */
    const RDIERROR_SRC_WEBP_ALPHA_NOTSUPPORTED = 1;
    /**
     * Source file is not image.
     */
    const RDIERROR_SRC_NOTIMAGE = 2;
    /**
     * Source file is not exists.
     */
    const RDIERROR_SRC_NOTEXISTS = 3;
    /**
     * Unable to set source from this kind of image.
     */
    const RDIERROR_SRC_UNKNOWN = 16;
    /**
     * Unable to calculated, please try to calculate on your own and use `resizeNoRatio()` instead.
     */
    const RDIERROR_CALCULATEFAILED_USE_RESIZENORATIO = 4;
    /**
     * Unable to crop this kind of image.
     */
    const RDIERROR_CROP_UNKNOWNIMG = 5;
    /**
     * Unable to resize this kind of image.
     */
    const RDIERROR_RESIZE_UNKNOWIMG = 6;
    /**
     * Unable to rotate this kind of image.
     */
    const RDIERROR_ROTATE_UNKNOWIMG = 7;
    /**
     * Unable to flip this kind of image.
     */
    const RDIERROR_FLIP_UNKNOWIMG = 8;
    /**
     * Not support flip for current PHP version. (PHP 5.5 or newer are required to use GD flip.)
     */
    const RDIERROR_FLIP_NOTSUPPORTED = 9;
    /**
     * Unable to set watermark from this kind of image.
     */
    const RDIERROR_WMI_UNKNOWIMG = 10;
    /**
     * Watermark image not exists.
     */
    const RDIERROR_WMI_NOTEXISTS = 11;
    /**
     * Unable to save to this extension. or target file extension is not supported.
     */
    const RDIERROR_SAVE_UNSUPPORT = 12;
    /**
     * Failed to save an image.
     */
    const RDIERROR_SAVE_FAILED = 13;
    /**
     * Unable to show image in this extension. File extension is not supported.
     */
    const RDIERROR_SHOW_UNSUPPORT = 14;
    /**
     * Failed to show an image to browser.
     */
    const RDIERROR_SHOW_FAILED = 15;
    /**
     * Watermark text font file is not exists.
     */
    const RDIERROR_WMT_FONT_NOTEXISTS = 17;
    /**
     * Imagick extension was not loaded.
     */
    const RDIERROR_IMAGICK_NOTLOAD = 18;
    /**
     * Imagick version does not meet requirement.
     */
    const RDIERROR_IMAGICK_NOTMEETREQUIREMENT = 19;
    /**
     * Could not verify Imagick version.
     */
    const RDIERROR_IMAGICK_VERSIONUNKNOW = 20;
    /**
     * Image Magick version does not meet requirement.
     */
    const RDIERROR_IMAGEMAGICK_NOTMEETREQUIREMENT = 21;


}
