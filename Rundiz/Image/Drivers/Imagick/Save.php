<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Drivers\Imagick;


/**
 * Save command class.
 * 
 * @since 3.1.0
 */
class Save extends \Rundiz\Image\Drivers\AbstractImagickCommand
{


    use \Rundiz\Image\Drivers\Traits\ImagickTrait;


    public function execute($file_name)
    {
        $FS = new \Rundiz\Image\FileSystem();
        $file_name = $FS->getFileRealpath($file_name);
        $check_file_ext = strtolower($FS->getFileExtension($file_name));
        unset($FS);

        // save to file. each image types use different ways to save.
        if ($check_file_ext === 'gif') {
            // if save to gif
            // in case that source image is PNG and have transparency, then this is no problem. just keep transparent.

            if ($this->ImagickD->source_image_type === IMAGETYPE_GIF && $this->ImagickD->save_animate_gif === true) {
                // if source file is gif and allow to save animated
                $save_result = $this->ImagickD->Imagick->writeImages($file_name, true);
            } else {
                if ($this->ImagickD->source_image_frames > 1 && is_object($this->ImagickD->ImagickFirstFrame)) {
                    // if source image is animated gif and save to non-animated gif
                    // get the first frame.
                    $this->getFirstFrame();
                }

                if ($this->ImagickD->source_image_type !== IMAGETYPE_GIF) {
                    // if source image is other than gif, it is required to set image page.
                    $this->ImagickD->Imagick->setImagePage(0, 0, 0, 0);
                }

                $save_result = $this->ImagickD->Imagick->writeImage($file_name);
            }
        } elseif ($check_file_ext === 'jpg') {
            // if save to jpg
            if ($this->ImagickD->source_image_type === IMAGETYPE_GIF) {
                // if source file is gif
                if ($this->ImagickD->source_image_frames > 1 && is_object($this->ImagickD->ImagickFirstFrame)) {
                    // if source image is animated gif and save to jpg
                    // get the first frame.
                    $this->getFirstFrame();
                }

                // covnert from transparent to white before save
                $this->fillWhiteToImage();
            } elseif ($this->ImagickD->source_image_type === IMAGETYPE_PNG) {
                // if source file is png
                // set image page.
                $this->ImagickD->Imagick->setImagePage(0, 0, 0, 0);
                // convert from transparent to white before save
                $this->fillWhiteToImage();
            }

            $this->ImagickD->jpg_quality = intval($this->ImagickD->jpg_quality);
            if ($this->ImagickD->jpg_quality < 1) {
                // if quality is less than 1.
                // `setImageCompressionQuality()` support minimum 1, not 0.
                $this->ImagickD->jpg_quality = 1;
            }
            if ($this->ImagickD->jpg_quality > 100) {
                $this->ImagickD->jpg_quality = 100;
            }

            $this->ImagickD->Imagick->setImageCompressionQuality($this->ImagickD->jpg_quality);
            $save_result = $this->ImagickD->Imagick->writeImage($file_name);
        } elseif ($check_file_ext === 'png') {
            // if save to png
            if ($this->ImagickD->source_image_type === IMAGETYPE_GIF) {
                // if source file is gif
                if ($this->ImagickD->source_image_frames > 1 && is_object($this->ImagickD->ImagickFirstFrame)) {
                    // if source image is animated gif and save to png
                    // get the first frame.
                    $this->getFirstFrame();
                }

                // keep transparency from gif without any modification.
            }

            // png compression
            $this->ImagickD->png_quality = intval($this->ImagickD->png_quality);
            if ($this->ImagickD->png_quality < 0 || $this->ImagickD->png_quality > 9) {
                $this->ImagickD->png_quality = 0;
            }
            $this->ImagickD->Imagick->setCompressionQuality(intval($this->ImagickD->png_quality . 5));

            $save_result = $this->ImagickD->Imagick->writeImage($file_name);
        } elseif ($check_file_ext === 'webp') {
            // if save to webp
            $this->ImagickD->jpg_quality = intval($this->ImagickD->jpg_quality);
            if ($this->ImagickD->jpg_quality < 1) {
                // if quality is less than 1.
                // `setImageCompressionQuality()` support minimum 1, not 0.
                $this->ImagickD->jpg_quality = 1;
            }
            if ($this->ImagickD->jpg_quality > 100) {
                $this->ImagickD->jpg_quality = 100;
            }

            $this->ImagickD->Imagick->setImageCompressionQuality($this->ImagickD->jpg_quality);
            $save_result = $this->ImagickD->Imagick->writeImage($file_name);
        } else {
            $this->ImagickD->status = false;
            $this->ImagickD->status_msg = sprintf('Unable to save this kind of image. (%s)', $check_file_ext);
            return false;
        }

        if (isset($save_result) && $save_result !== false) {
            $this->ImagickD->status = true;
            $this->ImagickD->status_msg = null;
            return true;
        } else {
            $this->ImagickD->status = false;
            $this->ImagickD->status_msg = 'Failed to save the image.';
            return false;
        }
    }// execute


}
