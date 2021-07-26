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


    public function execute($file_name)
    {
        $FS = new \Rundiz\Image\FileSystem();
        $file_name = $FS->getFileRealpath($file_name);
        $check_file_ext = strtolower($FS->getFileExtension($file_name));
        unset($FS);

        // save to file. each image types use different ways to save.
        if ($check_file_ext == 'gif') {
            if ($this->ImagickD->source_image_type === IMAGETYPE_PNG) {
                // source file is png
                // convert from transparent to white before save
                $this->ImagickD->Imagick->setImagePage(0, 0, 0, 0);
                $this->ImagickD->Imagick->setImageBackgroundColor('white');
                $this->ImagickD->Imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
                $this->ImagickD->Imagick = $this->ImagickD->Imagick->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);
            }

            if ($this->ImagickD->source_image_type === IMAGETYPE_GIF && $this->ImagickD->save_animate_gif === true) {
                // source file is gif and allow to save animated
                $save_result = $this->ImagickD->Imagick->writeImages($file_name, true);
            } else {
                if ($this->ImagickD->source_image_frames > 1 && is_object($this->ImagickD->ImagickFirstFrame)) {
                    // if source image is animated gif and save to non-animated gif, get the first frame.
                    $this->ImagickD->Imagick->clear();
                    $this->ImagickD->Imagick = $this->ImagickD->ImagickFirstFrame;
                    $this->ImagickD->ImagickFirstFrame = null;
                }

                if ($this->ImagickD->source_image_type !== IMAGETYPE_GIF) {
                    // source image is other than gif, it is required to set image page.
                    $this->ImagickD->Imagick->setImagePage(0, 0, 0, 0);
                }

                $save_result = $this->ImagickD->Imagick->writeImage($file_name);
            }
        } elseif ($check_file_ext == 'jpg') {
            if ($this->ImagickD->source_image_type === IMAGETYPE_GIF) {
                // source file is gif
                if ($this->ImagickD->source_image_frames > 1 && is_object($this->ImagickD->ImagickFirstFrame)) {
                    // if source image is animated gif and save to non-animated gif, get the first frame.
                    $this->ImagickD->Imagick->clear();
                    $this->ImagickD->Imagick = $this->ImagickD->ImagickFirstFrame;
                    $this->ImagickD->ImagickFirstFrame = null;
                }

                // covnert from transparent to white before save
                $this->ImagickD->Imagick->setImageBackgroundColor('white');
                $this->ImagickD->Imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
                $this->ImagickD->Imagick = $this->ImagickD->Imagick->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);
            } elseif ($this->ImagickD->source_image_type === IMAGETYPE_PNG) {
                // source file is png
                // convert from transparent to white before save
                $this->ImagickD->Imagick->setImagePage(0, 0, 0, 0);
                $this->ImagickD->Imagick->setImageBackgroundColor('white');
                $this->ImagickD->Imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
                $this->ImagickD->Imagick = $this->ImagickD->Imagick->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);
            }

            $this->ImagickD->jpg_quality = intval($this->ImagickD->jpg_quality);
            if ($this->ImagickD->jpg_quality < 0 || $this->ImagickD->jpg_quality > 100) {
                $this->ImagickD->jpg_quality = 100;
            }

            $this->ImagickD->Imagick->setImageCompressionQuality($this->ImagickD->jpg_quality);
            $save_result = $this->ImagickD->Imagick->writeImage($file_name);
        } elseif ($check_file_ext == 'png') {
            if ($this->ImagickD->source_image_type === IMAGETYPE_GIF) {
                // source file is gif
                if ($this->ImagickD->source_image_frames > 1 && is_object($this->ImagickD->ImagickFirstFrame)) {
                    // if source image is animated gif and save to non-animated gif, get the first frame.
                    $this->ImagickD->Imagick->clear();
                    $this->ImagickD->Imagick = $this->ImagickD->ImagickFirstFrame;
                    $this->ImagickD->ImagickFirstFrame = null;
                }

                // covnert from transparent to white before save
                $this->ImagickD->Imagick->setImageBackgroundColor('white');
                $this->ImagickD->Imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
                $this->ImagickD->Imagick = $this->ImagickD->Imagick->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);
            }

            // png compression
            $this->ImagickD->png_quality = intval($this->ImagickD->png_quality);
            if ($this->ImagickD->png_quality < 0 || $this->ImagickD->png_quality > 9) {
                $this->ImagickD->png_quality = 0;
            }
            $this->ImagickD->Imagick->setCompressionQuality(intval($this->ImagickD->png_quality . 5));

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
