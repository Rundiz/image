<?php


namespace Rundiz\Image\Tests\ImageProcessTests;


class ImageGdTest extends \Rundiz\Image\Tests\RDICommonTestCase
{


    /**
     * Remove animated gif source from list for testing with GD class.
     * 
     * @return array
     */
    private function gdTestSkipAnimatedGif()
    {
        if (is_array(static::$source_images_set)) {
            $source_images_set = static::$source_images_set;
            $i = 0;
            foreach ($source_images_set as $source_image) {
                if (strpos($source_image, '-animated') !== false) {
                    unset($source_images_set[$i]);
                }
                $i++;
            }
            unset($i, $source_image);
            return $source_images_set;
        }
        return static::$source_images_set;
    }// gdTestSkipAnimatedGif


    #[Depends('Rundiz\Image\Tests\DependentTests\DirsFilesExistsTest::testImageExists')]
    public function testGdResize()
    {
        if (is_array($source_images_set = $this->gdTestSkipAnimatedGif())) {
            $resize_width = 400;
            $resize_height = 300;
            foreach ($source_images_set as $source_image) {
                $Image = new \Rundiz\Image\Drivers\Gd(static::$source_images_dir.$source_image);
                foreach (static::$processed_extensions as $save_extension) {
                    $Image->master_dim = 'width';
                    $Image->resize($resize_width, $resize_height);
                    $save_file_name = static::$processed_images_dir.'rundiz-gd-source['.$this->getExtensionFromName($source_image).']-masterdim['.$Image->master_dim.']-resize['.$resize_width.'x'.$resize_height.'].'.$save_extension;
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
                $Image = new \Rundiz\Image\Drivers\Gd(static::$source_images_dir.$source_image);
                foreach (static::$processed_extensions as $save_extension) {
                    $Image->master_dim = 'height';
                    $Image->resize($resize_width, $resize_height);
                    $save_file_name = static::$processed_images_dir.'rundiz-gd-source['.$this->getExtensionFromName($source_image).']-masterdim['.$Image->master_dim.']-resize['.$resize_width.'x'.$resize_height.'].'.$save_extension;
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
    }// testGdResize


    #[Depends('Rundiz\Image\Tests\DependentTests\DirsFilesExistsTest::testImageExists')]
    public function testGdRotate()
    {
        if (is_array($source_images_set = $this->gdTestSkipAnimatedGif())) {
            $resize_width = 400;
            $resize_height = 300;
            $rotate = 270;
            foreach ($source_images_set as $source_image) {
                $Image = new \Rundiz\Image\Drivers\Gd(static::$source_images_dir.$source_image);
                foreach (static::$processed_extensions as $save_extension) {
                    $Image->master_dim = 'auto';
                    $Image->resizeNoRatio($resize_width, $resize_height);
                    $Image->rotate($rotate);
                    $save_file_name = static::$processed_images_dir.'rundiz-gd-source['.$this->getExtensionFromName($source_image).']-masterdim['.$Image->master_dim.']-resizeNoRatio['.$resize_width.'x'.$resize_height.']-rotate['.$rotate.']'.'.'.$save_extension;
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
    }// testGdRotate


    #[Depends('Rundiz\Image\Tests\DependentTests\DirsFilesExistsTest::testImageExists')]
    public function testGdCrop()
    {
        if (is_array($source_images_set = $this->gdTestSkipAnimatedGif())) {
            $resize_width = 900;
            $resize_height = 600;
            $crop_width = 400;
            $crop_height = 400;
            $crop_x = 'center';
            $crop_y = 'middle';
            foreach ($source_images_set as $source_image) {
                $Image = new \Rundiz\Image\Drivers\Gd(static::$source_images_dir.$source_image);
                foreach (static::$processed_extensions as $save_extension) {
                    $Image->master_dim = 'auto';
                    $Image->resizeNoRatio($resize_width, $resize_height);
                    $Image->crop($crop_width, $crop_height, $crop_x, $crop_y);
                    $save_file_name = static::$processed_images_dir.'rundiz-gd-source['.$this->getExtensionFromName($source_image).']-masterdim['.$Image->master_dim.']-resizeNoRatio['.$resize_width.'x'.$resize_height.']-crop['.$crop_width.'x'.$crop_height.'-'.$crop_x.','.$crop_y.']'.'.'.$save_extension;
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
    }// testGdCrop


    #[Depends('Rundiz\Image\Tests\DependentTests\DirsFilesExistsTest::testImageExists')]
    public function testGdWatermarkImage()
    {
        if (is_array($source_images_set = $this->gdTestSkipAnimatedGif())) {
            $resize_width = 900;
            $resize_height = 600;
            $watermark_x = 'center';
            $watermark_y = 'middle';
            foreach ($source_images_set as $source_image) {
                $Image = new \Rundiz\Image\Drivers\Gd(static::$source_images_dir.$source_image);
                foreach (static::$processed_extensions as $save_extension) {
                    foreach (static::$source_watermark_images_set as $watermark_image) {
                        $Image->master_dim = 'auto';
                        $Image->resizeNoRatio($resize_width, $resize_height);
                        $Image->watermarkImage(static::$source_images_dir.$watermark_image, $watermark_x, $watermark_y);
                        $save_file_name = static::$processed_images_dir.'rundiz-gd-source['.$this->getExtensionFromName($source_image).']-masterdim['.$Image->master_dim.']-resizeNoRatio['.$resize_width.'x'.$resize_height.']-watermarkImage['.$this->getExtensionFromName($watermark_image).'-'.$watermark_x.','.$watermark_y.']'.'.'.$save_extension;
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
    }// testGdWatermarkImage


    #[Depends('Rundiz\Image\Tests\DependentTests\DirsFilesExistsTest::testImageExists')]
    public function testGdWatermarkText()
    {
        if (is_array($source_images_set = $this->gdTestSkipAnimatedGif())) {
            $resize_width = 900;
            $resize_height = 600;
            $watermark_x = 'center';
            $watermark_y = 'middle';
            foreach ($source_images_set as $source_image) {
                $Image = new \Rundiz\Image\Drivers\Gd(static::$source_images_dir.$source_image);
                foreach (static::$processed_extensions as $save_extension) {
                    foreach (static::$source_watermark_fonts_set as $watermark_font) {
                        $Image->master_dim = 'auto';
                        $Image->resizeNoRatio($resize_width, $resize_height);
                        $Image->watermarkText(static::$watermark_text, static::$source_images_dir.$watermark_font, $watermark_x, $watermark_y, 18);
                        $save_file_name = static::$processed_images_dir.'rundiz-gd-source['.$this->getExtensionFromName($source_image).']-masterdim['.$Image->master_dim.']-resizeNoRatio['.$resize_width.'x'.$resize_height.']-watermarkText['.$watermark_font.'-'.$watermark_x.','.$watermark_y.']'.'.'.$save_extension;
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
    }// testGdWatermarkText


}
