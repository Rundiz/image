<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Drivers\Imagick;


/**
 * Show image to browser.
 * 
 * @since 3.1.0
 */
class Show extends \Rundiz\Image\Drivers\AbstractImagickCommand
{


    public function execute($file_ext = '')
    {
        if ($file_ext == null) {
            $file_ext = str_replace('.', '', $this->source_image_ext);
        }
        $file_ext = str_ireplace('jpeg', 'jpg', $file_ext);
        $file_ext = ltrim($file_ext, '.');

        $check_file_ext = strtolower($file_ext);

        // show image to browser.
        // http://php.net/manual/en/imagick.getimageblob.php for single frame of image or non-animated picture.
        // http://php.net/manual/en/imagick.getimagesblob.php for animated gif.
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
                // source file is gif and allow to show animated
                $show_result = $this->ImagickD->Imagick->getImagesBlob();
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

                $this->ImagickD->Imagick->setImageFormat('gif');
                $show_result = $this->ImagickD->Imagick->getImageBlob();
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

            $this->ImagickD->Imagick->setImageFormat('jpg');
            $this->ImagickD->Imagick->setImageCompressionQuality($this->ImagickD->jpg_quality);
            $show_result = $this->ImagickD->Imagick->getImageBlob();
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

            $this->ImagickD->Imagick->setImageFormat('png');
            $show_result = $this->ImagickD->Imagick->getImageBlob();
        } else {
            $this->ImagickD->status = false;
            $this->ImagickD->status_msg = 'Unable to show this kind of image.';
            return false;
        }

        // clear
        unset($check_file_ext, $file_ext);

        if ($show_result !== false) {
            $this->ImagickD->status = true;
            $this->ImagickD->status_msg = null;
            // Because in PHP GD it is automatically show the image content by omit the file name in `imagexxx()` function without echo command.
            // But in PHP Imagick must echo content that have got from getImageBlob() of Imagick class, then we have to echo it here to make this image class work in the same way.
            echo $show_result;
            return true;
        } else {
            $this->ImagickD->status = false;
            $this->ImagickD->status_msg = 'Failed to show the image.';
            return false;
        }
    }// execute


}
