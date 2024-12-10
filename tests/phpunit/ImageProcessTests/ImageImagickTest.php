<?php


namespace Rundiz\Image\Tests\ImageProcessTests;


class ImageImagickTest extends \Rundiz\Image\Tests\RDICommonTestCase
{


    #[Depends('Rundiz\Image\Tests\DependentTests\DirsFilesExistsTest::testImageExists')]
    public function testRequiredImagickImageMagickPhpVersions()
    {
        if (extension_loaded('imagick') === true) {
            $Image = new \Rundiz\Image\Drivers\Imagick(static::$source_images_dir.static::$source_images_set[0]);
            $this->assertTrue($Image->status === true && $Image->status_msg == null, sprintf('The required Imagick version, ImageMagick version are not met. "%s"', $Image->status_msg));
            unset($Image);
        } else {
            $this->markTestIncomplete('You did not have Imagick extension for PHP installed. This test is incomplete or you can just skip it.');
        }
    }// testRequiredImagickImageMagickPhpVersions


    #[Depends('testRequiredImagickImageMagickPhpVersions')]
    public function testImagickResize()
    {
        if (is_array($source_images_set = static::$source_images_set)) {
            $resize_width = 400;
            $resize_height = 300;
            foreach ($source_images_set as $source_image) {
                $Image = new \Rundiz\Image\Drivers\Imagick(static::$source_images_dir.$source_image);
                foreach (static::$processed_extensions as $save_extension) {
                    $Image->master_dim = 'width';
                    $Image->resize($resize_width, $resize_height);
                    $save_file_name = static::$processed_images_dir.'rundiz-imagick-source['.$this->getExtensionFromName($source_image).']-masterdim['.$Image->master_dim.']-resize['.$resize_width.'x'.$resize_height.'].'.$save_extension;
                    $Image->save($save_file_name);
                    $Image->clear();
                    list($width, $height, $image_type) = getimagesize($save_file_name);
                    $processed_image_data = array('width' => $width, 'image_type' => $image_type);

                    // test assert.
                    $this->assertTrue(
                        empty(\Rundiz\Image\Tests\Helpers\Arrays::array_diff_assoc_recursive(
                            array(
                                'width' => $resize_width, 
                                'image_type' => $this->getProcessedExtensionTypeNumber($save_extension)
                            ), 
                            $processed_image_data
                        )),
                        'Image ' . $source_image . ' has unexpected resized value or image type.'
                    );
                    unset($height, $width, $image_type, $save_file_name);
                }// endforeach;
                unset($save_extension);
            }// endforeach;
            unset($source_image);

            foreach ($source_images_set as $source_image) {
                $Image = new \Rundiz\Image\Drivers\Imagick(static::$source_images_dir.$source_image);
                foreach (static::$processed_extensions as $save_extension) {
                    $Image->master_dim = 'height';
                    $Image->resize($resize_width, $resize_height);
                    $save_file_name = static::$processed_images_dir.'rundiz-imagick-source['.$this->getExtensionFromName($source_image).']-masterdim['.$Image->master_dim.']-resize['.$resize_width.'x'.$resize_height.'].'.$save_extension;
                    $Image->save($save_file_name);
                    $Image->clear();
                    list($width, $height, $image_type) = getimagesize($save_file_name);
                    $processed_image_data = array('height' => $height, 'image_type' => $image_type);

                    // test assert.
                    $this->assertTrue(
                        empty(\Rundiz\Image\Tests\Helpers\Arrays::array_diff_assoc_recursive(
                            array(
                                'height' => $resize_height, 
                                'image_type' => $this->getProcessedExtensionTypeNumber($save_extension)
                            ), 
                            $processed_image_data
                        )),
                        'Image ' . $source_image . ' has unexpected resized value or image type.'
                    );
                    unset($height, $width, $image_type, $save_file_name);
                }// endforeach;
                unset($save_extension);
            }// endforeach;
            unset($source_image);

            unset($resize_height, $resize_width, $source_images_set);
        }
    }// testImagickResize


    #[Depends('testRequiredImagickImageMagickPhpVersions')]
    public function testImagickRotate()
    {
        if (is_array($source_images_set = static::$source_images_set)) {
            $resize_width = 400;
            $resize_height = 300;
            $rotate = 270;
            foreach ($source_images_set as $source_image) {
                $Image = new \Rundiz\Image\Drivers\Imagick(static::$source_images_dir.$source_image);
                foreach (static::$processed_extensions as $save_extension) {
                    $Image->master_dim = 'auto';
                    $Image->resizeNoRatio($resize_width, $resize_height);
                    $Image->rotate($rotate);
                    $save_file_name = static::$processed_images_dir.'rundiz-imagick-source['.$this->getExtensionFromName($source_image).']-masterdim['.$Image->master_dim.']-resizeNoRatio['.$resize_width.'x'.$resize_height.']-rotate['.$rotate.']'.'.'.$save_extension;
                    $Image->save($save_file_name);
                    $Image->clear();
                    list($width, $height, $image_type) = getimagesize($save_file_name);
                    $processed_image_data = array('width' => $width, 'image_type' => $image_type);

                    // test assert.
                    $this->assertTrue(
                        empty(\Rundiz\Image\Tests\Helpers\Arrays::array_diff_assoc_recursive(
                            array(
                                'width' => $resize_height, 
                                'image_type' => $this->getProcessedExtensionTypeNumber($save_extension)
                            ), 
                            $processed_image_data
                        )),
                        'Image ' . $source_image . ' has unexpected processed dimension or image type.'
                    );
                    unset($height, $width, $image_type, $save_file_name);
                }// endforeach;
                unset($save_extension);
            }// endforeach;
            unset($source_image);

            unset($resize_height, $resize_width, $rotate, $source_images_set);
        }
    }// testImagickRotate


    #[Depends('testRequiredImagickImageMagickPhpVersions')]
    public function testImagickCrop()
    {
        if (is_array($source_images_set = static::$source_images_set)) {
            $resize_width = 900;
            $resize_height = 600;
            $crop_width = 400;
            $crop_height = 400;
            $crop_x = 'center';
            $crop_y = 'middle';
            foreach ($source_images_set as $source_image) {
                $Image = new \Rundiz\Image\Drivers\Imagick(static::$source_images_dir.$source_image);
                foreach (static::$processed_extensions as $save_extension) {
                    $Image->master_dim = 'auto';
                    $Image->resizeNoRatio($resize_width, $resize_height);
                    $Image->crop($crop_width, $crop_height, $crop_x, $crop_y);
                    $save_file_name = static::$processed_images_dir.'rundiz-imagick-source['.$this->getExtensionFromName($source_image).']-masterdim['.$Image->master_dim.']-resizeNoRatio['.$resize_width.'x'.$resize_height.']-crop['.$crop_width.'x'.$crop_height.'-'.$crop_x.','.$crop_y.']'.'.'.$save_extension;
                    $Image->save($save_file_name);
                    $Image->clear();
                    list($width, $height, $image_type) = getimagesize($save_file_name);
                    $processed_image_data = array('width' => $width, 'image_type' => $image_type);

                    // test assert.
                    $this->assertTrue(
                        empty(\Rundiz\Image\Tests\Helpers\Arrays::array_diff_assoc_recursive(
                            array(
                                'width' => $crop_width, 
                                'image_type' => $this->getProcessedExtensionTypeNumber($save_extension)
                            ), 
                            $processed_image_data
                        )),
                        'Image ' . $source_image . ' has unexpected processed dimension or image type.'
                    );
                    unset($height, $width, $image_type, $save_file_name);
                }// endforeach;
                unset($save_extension);
            }// endforeach;
            unset($source_image);

            unset($crop_height, $crop_width, $crop_x, $crop_y, $resize_height, $resize_width, $source_images_set);
        }
    }// testImagickCrop


    #[Depends('testRequiredImagickImageMagickPhpVersions')]
    public function testImagickWatermarkImage()
    {
        if (is_array($source_images_set = static::$source_images_set)) {
            $resize_width = 900;
            $resize_height = 600;
            $watermark_x = 'center';
            $watermark_y = 'middle';
            foreach ($source_images_set as $source_image) {
                $Image = new \Rundiz\Image\Drivers\Imagick(static::$source_images_dir.$source_image);
                foreach (static::$processed_extensions as $save_extension) {
                    foreach (static::$source_watermark_images_set as $watermark_image) {
                        $Image->master_dim = 'auto';
                        $Image->resizeNoRatio($resize_width, $resize_height);
                        $Image->watermarkImage(static::$source_images_dir.$watermark_image, $watermark_x, $watermark_y);
                        $save_file_name = static::$processed_images_dir.'rundiz-imagick-source['.$this->getExtensionFromName($source_image).']-masterdim['.$Image->master_dim.']-resizeNoRatio['.$resize_width.'x'.$resize_height.']-watermarkImage['.$this->getExtensionFromName($watermark_image).'-'.$watermark_x.','.$watermark_y.']'.'.'.$save_extension;
                        $Image->save($save_file_name);
                        $Image->clear();
                        list($width, $height, $image_type) = getimagesize($save_file_name);
                        $processed_image_data = array('width' => $width, 'image_type' => $image_type);

                        // test assert.
                        $this->assertTrue(
                            empty(\Rundiz\Image\Tests\Helpers\Arrays::array_diff_assoc_recursive(
                                array(
                                    'width' => $resize_width, 
                                    'image_type' => $this->getProcessedExtensionTypeNumber($save_extension)
                                ), 
                                $processed_image_data
                            )),
                            'Source image ' . $source_image . ' and watermark image ' . $watermark_image . ' has unexpected processed dimension or image type.'
                        );
                        unset($height, $width, $image_type, $save_file_name);
                    }// endforeach;
                    unset($watermark_image);
                }// endforeach;
                unset($save_extension);
            }// endforeach;
            unset($source_image);

            unset($resize_height, $resize_width, $source_images_set, $watermark_x, $watermark_y);
        }
    }// testImagickWatermarkImage


    #[Depends('testRequiredImagickImageMagickPhpVersions')]
    public function testImagickWatermarkText()
    {
        if (is_array($source_images_set = static::$source_images_set)) {
            $resize_width = 900;
            $resize_height = 600;
            $watermark_x = 'center';
            $watermark_y = 'middle';
            foreach ($source_images_set as $source_image) {
                $Image = new \Rundiz\Image\Drivers\Imagick(static::$source_images_dir.$source_image);
                foreach (static::$processed_extensions as $save_extension) {
                    foreach (static::$source_watermark_fonts_set as $watermark_font) {
                        $Image->master_dim = 'auto';
                        $Image->resizeNoRatio($resize_width, $resize_height);
                        $Image->watermarkText(static::$watermark_text, static::$source_images_dir.$watermark_font, $watermark_x, $watermark_y, 18);
                        $save_file_name = static::$processed_images_dir.'rundiz-imagick-source['.$this->getExtensionFromName($source_image).']-masterdim['.$Image->master_dim.']-resizeNoRatio['.$resize_width.'x'.$resize_height.']-watermarkText['.$watermark_font.'-'.$watermark_x.','.$watermark_y.']'.'.'.$save_extension;
                        $Image->save($save_file_name);
                        $Image->clear();
                        list($width, $height, $image_type) = getimagesize($save_file_name);
                        $processed_image_data = array('width' => $width, 'image_type' => $image_type);

                        // test assert.
                        $this->assertTrue(
                            empty(\Rundiz\Image\Tests\Helpers\Arrays::array_diff_assoc_recursive(
                                array(
                                    'width' => $resize_width, 
                                    'image_type' => $this->getProcessedExtensionTypeNumber($save_extension)
                                ), 
                                $processed_image_data
                            )),
                            'Source image ' . $source_image . ' and watermark font ' . $watermark_font . ' has unexpected processed dimension or image type.'
                        );
                        unset($height, $width, $image_type, $save_file_name);
                    }// endforeach;
                    unset($watermark_font);
                }// endforeach;
                unset($save_extension);
            }// endforeach;
            unset($source_image);

            unset($resize_height, $resize_width, $source_images_set, $watermark_x, $watermark_y);
        }
    }// testImagickWatermarkText


}
