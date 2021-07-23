<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Tests;


class ExtendedImageAbstractClass extends \Rundiz\Image\ImageAbstractClass
{


    /**
     * {@inheritDoc}
     */
    public function buildSourceImageData($source_image_path)
    {
        return parent::buildSourceImageData($source_image_path);
    }// buildSourceImageData


    /**
     * {@inheritDoc}
     */
    public function calculateImageSizeRatio($width, $height)
    {
        return parent::calculateImageSizeRatio($width, $height);
    }// calculateImageSizeRatio


    //put your code here
    public function clear()
    {
    }


    public function crop($width, $height, $start_x = '0', $start_y = '0', $fill = 'transparent')
    {
    }


    /**
     * {@inheritDoc}
     */
    public function getImageFileData($imagePath)
    {
        return parent::getImageFileData($imagePath);
    }// getImageFileData


    /**
     * {@inheritDoc}
     */
    public function getImageSize()
    {
        return parent::getImageSize();
    }// getImageSize


    /**
     * {@inheritDoc}
     */
    public function getSourceImageOrientation()
    {
        return parent::getSourceImageOrientation();
    }// getSourceImageOrientation


    /**
     * {@inheritDoc}
     */
    public function isClassSetup()
    {
        return parent::isClassSetup();
    }// isClassSetup


    public function resize($width, $height)
    {
    }


    public function resizeNoRatio($width, $height)
    {
    }


    public function rotate($degree = 90)
    {
    }


    public function save($file_name)
    {
    }


    public function show($file_ext = '')
    {
    }


    public function watermarkImage($wm_img_path, $wm_img_start_x = 0, $wm_img_start_y = 0)
    {
    }


    public function watermarkText($wm_txt_text, $wm_txt_font_path, $wm_txt_start_x = 0, $wm_txt_start_y = 0, $wm_txt_font_size = 10, $wm_txt_font_color = 'transwhitetext', $wm_txt_font_alpha = 60)
    {
    }


}
