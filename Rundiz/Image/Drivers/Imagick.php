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
 * @todo Still working on it.
 */
class Imagick extends ImageAbstractClass
{


    /**
     * {@inheritDoc}
     */
    public function __construct($source_image_path)
    {
        return parent::__construct($source_image_path);
    }// __construct


    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        
    }// clear


    /**
     * {@inheritDoc}
     */
    public function crop($width, $height, $start_x = '0', $start_y = '0', $fill = 'transparent')
    {
        
    }// crop


    /**
     * {@inheritDoc}
     */
    public function getImageSize()
    {
        
    }// getImageSize


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
        
    }// resizeNoRatio


    /**
     * {@inheritDoc}
     */
    public function rotate($degree = '90')
    {
        
    }// rotate


    /**
     * {@inheritDoc}
     */
    public function save($file_name)
    {
        
    }// save


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
