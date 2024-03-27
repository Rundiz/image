<?php
/**
 * PHP Image manipulation class.
 * 
 * @package Image
 * @version 3.1.4dev-20240327
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 */


namespace Rundiz\Image;

/**
 * Interface for image manipulation drivers
 * 
 * @since 3.0
 */
interface ImageInterface
{


    /**
     * Clear and reset processed results to make it ready for new process while keep the original source.
     * 
     * @return bool Return true on successfully cleared.
     */
    public function clear();


    /**
     * Crop the image.
     * 
     * @param int $width Image width of cropping image. Size in pixels.
     * @param int $height Image height of cropping image. Size in pixels.
     * @param mixed $start_x Position to begin in x axis. The value is integer or 'center' for automatically find center.
     * @param mixed $start_y Position to begin in y axis. The value is integer or 'middle' for automatically find middle.
     * @param string $fill Color of background that will be filled in case that image has transparent in it. The value is transparent, white, black (for gif and png).
     * @return bool Return true on success, false on failed. Call to status_msg property to see the details on failure.
     */
    public function crop($width, $height, $start_x = '0', $start_y = '0', $fill = 'transparent');


    /**
     * Get source image size
     * 
     * @return array Return array of image size with 'width' and 'height' in array key.
     */
    public function getImageSize();


    /**
     * Resize the image.
     * 
     * @param int $width Image width that image will be resize to.
     * @param int $height Image height that image will be resize to.
     * @return bool Return true on success, false on failed. Call to status_msg property to see the details on failure.
     */
    public function resize($width, $height);


    /**
     * Resize the image by ignoring aspect ratio.
     * 
     * @param int $width Image width that image will be resize to.
     * @param int $height Image height that image will be resize to.
     * @return bool Return true on success, false on failed. Call to status_msg property to see the details on failure.
     */
    public function resizeNoRatio($width, $height);


    /**
     * Rotate the image.
     * 
     * @param int|string $degree Degree of rotation (0, 90, 180, 270). For php >= 5.5, you can use 'hor', 'vrt', 'horvrt' as degree to flip the image.
     * @return bool Return true on success, false on failed. Call to status_msg property to see the details on failure.
     */
    public function rotate($degree = 90);


    /**
     * Save the image to file.
     * 
     * @param string $file_name Path to save image to. Please including file extension.
     * @return bool Return true on success, false on failed. Call to status_msg property to see the details on failure.
     */
    public function save($file_name);


    /**
     * Show the image.
     * 
     * @param string $file_ext The file extension that this image will be display as.
     * @return mixed Show image content, return false on failed. Call to status_msg property to see the details on failure.
     */
    public function show($file_ext = '');


    /**
     * Set the watermark image.
     * 
     * @param string $wm_img_path Full path of watermark image file
     * @param int|string $wm_img_start_x Position to begin in x axis. The value is integer or 'left', 'center', 'right'.
     * @param int|string $wm_img_start_y Position to begin in y axis. The value is integer or 'top', 'middle', 'bottom'.
     * @param array $options The watermark options. (Since v3.1.3)<br>
     *              `padding` (int) Padding around watermark object. Use with left, right, bottom, top but not middle, center. See `\Rundiz\Image\Traits\CalculationTrait::calculateWatermarkImageStartXY()`.<br>
     * @return bool Return true on success, false on failed. Call to status_msg property to see the details on failure.
     */
    public function watermarkImage($wm_img_path, $wm_img_start_x = 0, $wm_img_start_y = 0, array $options = []);


    /**
     * Set the watermark text.
     * 
     * @param string $wm_txt_text Watermark text
     * @param string $wm_txt_font_path 'True Type Font' path
     * @param int|string $wm_txt_start_x Position to begin in x axis. The value is integer or 'left', 'center', 'right'.
     * @param int|string $wm_txt_start_y Position to begin in x axis. The value is integer or 'top', 'middle', 'bottom'.
     * @param int $wm_txt_font_size Font size
     * @param string $wm_txt_font_color Font color. ('black', 'white', 'red', 'green', 'blue', 'yellow', 'cyan', 'magenta', 'transwhitetext')
     * @param int $wm_txt_font_alpha Text transparency value. (0-127)
     * @param array $options The watermark text options. (Since v.3.1.0)<br>
     *              `fillBackground` (bool) Set to `true` to fill background color for text bounding box. Default is `false` to use transparent.<br>
     *              `backgroundColor` (string) The background color to fill for text bounding box. Available values are 'black', 'white', 'red', 'green', 'blue', 'yellow', 'cyan', 'magenta', 'debug'.<br>
     *              `padding` (int) (Since v3.1.3) Padding around watermark text. Use with left, right, bottom, top but not middle, center.<br>
     * @return bool Return true on success, false on failed. Call to status_msg property to see the details on failure.
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
    );


}
