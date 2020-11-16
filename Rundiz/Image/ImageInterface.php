<?php
/**
 * PHP Image manipulation class.
 * 
 * @package Image
 * @version 3.0.1
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
     * Clear and reset everything to make it ready for new call.
     * 
     * @return boolean Return true on successfully cleared.
     */
    public function clear();


    /**
     * Crop the image.
     * 
     * @param integer $width Image width of cropping image. Size in pixels.
     * @param integer $height Image height of cropping image. Size in pixels.
     * @param mixed $start_x Position to begin in x axis. The value is integer or 'center' for automatically find center.
     * @param mixed $start_y Position to begin in y axis. The value is integer or 'middle' for automatically find middle.
     * @param string $fill Color of background that will be filled in case that image has transparent in it. The value is transparent, white, black (for gif and png).
     * @return boolean Return true on success, false on failed. Call to status_msg property to see the details on failure.
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
     * @param integer $width Image width that image will be resize to.
     * @param integer $height Image height that image will be resize to.
     * @return boolean Return true on success, false on failed. Call to status_msg property to see the details on failure.
     */
    public function resize($width, $height);


    /**
     * Resize the image by ignoring aspect ratio.
     * 
     * @param integer $width Image width that image will be resize to.
     * @param integer $height Image height that image will be resize to.
     * @return boolean Return true on success, false on failed. Call to status_msg property to see the details on failure.
     */
    public function resizeNoRatio($width, $height);


    /**
     * Rotate the image.
     * 
     * @param integer|string $degree Degree of rotation (0, 90, 180, 270). For php >= 5.5, you can use 'hor', 'vrt', 'horvrt' as degree to flip the image.
     * @return boolean Return true on success, false on failed. Call to status_msg property to see the details on failure.
     */
    public function rotate($degree = 90);


    /**
     * Save the image to file.
     * 
     * @param string $file_name Path to save image to. Please including file extension.
     * @return boolean Return true on success, false on failed. Call to status_msg property to see the details on failure.
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
     * @param integer|string $wm_img_start_x Position to begin in x axis. The valus is integer or 'left', 'center', 'right'.
     * @param integer|string $wm_img_start_y Position to begin in x axis. The valus is integer or 'top', 'middle', 'bottom'.
     * @return boolean Return true on success, false on failed. Call to status_msg property to see the details on failure.
     */
    public function watermarkImage($wm_img_path, $wm_img_start_x = 0, $wm_img_start_y = 0);


    /**
     * Set the watermark text.
     * 
     * @param string $wm_txt_text Watermark text
     * @param string $wm_txt_font_path 'True Type Font' path
     * @param integer|string $wm_txt_start_x Position to begin in x axis. The valus is integer or 'left', 'center', 'right'.
     * @param integer|string $wm_txt_start_y Position to begin in x axis. The valus is integer or 'top', 'middle', 'bottom'.
     * @param integer $wm_txt_font_size Font size
     * @param string $wm_txt_font_color Font color. ('black', 'white', 'transwhitetext')
     * @param integer $wm_txt_font_alpha Text transparency value. (0-127)
     * @return boolean Return true on success, false on failed. Call to status_msg property to see the details on failure.
     */
    public function watermarkText($wm_txt_text, $wm_txt_font_path, $wm_txt_start_x = 0, $wm_txt_start_y = 0, $wm_txt_font_size = 10, $wm_txt_font_color = 'transwhitetext', $wm_txt_font_alpha = 60);


}
