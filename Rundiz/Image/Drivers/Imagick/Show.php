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


    use \Rundiz\Image\Drivers\Traits\ImagickTrait;


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
        if ($check_file_ext === 'gif') {
            // if save to gif
            // in case that source image is PNG and have transparency, then this is no problem. just keep transparent.

            if ($this->ImagickD->source_image_type === IMAGETYPE_GIF && $this->ImagickD->save_animate_gif === true) {
                // if source file is gif and allow to show animated
                $show_result = $this->ImagickD->Imagick->getImagesBlob();
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

                $this->ImagickD->Imagick->setImageFormat('gif');
                $show_result = $this->ImagickD->Imagick->getImageBlob();
            }
        } elseif ($check_file_ext === 'jpg') {
            // if save to jpg
            if ($this->ImagickD->source_image_type === IMAGETYPE_GIF) {
                // if source file is gif
                if ($this->ImagickD->source_image_frames > 1) {
                    // if source image is animated gif.
                    if (is_object($this->ImagickD->ImagickFirstFrame)) {
                        // if there is first frame object.
                        // get the first frame.
                        $this->getFirstFrame();
                    }
                }

                // covnert from transparent to white before save
                $this->fillWhiteToImage();
            } elseif (
                $this->ImagickD->source_image_type === IMAGETYPE_PNG || 
                $this->ImagickD->source_image_type === IMAGETYPE_WEBP
            ) {
                // if source file is png or webp
                // convert from transparent to white before save
                $this->fillWhiteToImage();
            }// endif;

            $this->ImagickD->jpg_quality = intval($this->ImagickD->jpg_quality);
            if ($this->ImagickD->jpg_quality < 1) {
                // if quality is less than 1.
                // `setImageCompressionQuality()` support minimum 1, not 0.
                $this->ImagickD->jpg_quality = 1;
            }
            if ($this->ImagickD->jpg_quality > 100) {
                $this->ImagickD->jpg_quality = 100;
            }

            $this->ImagickD->Imagick->setImageFormat('jpg');
            $this->ImagickD->Imagick->setImageCompressionQuality($this->ImagickD->jpg_quality);

            $show_result = $this->ImagickD->Imagick->getImageBlob();
        } elseif ($check_file_ext === 'png') {
            // if save to png
            if ($this->ImagickD->source_image_type === IMAGETYPE_GIF) {
                // if source file is gif
                if ($this->ImagickD->source_image_frames > 1) {
                    // if source image is animated gif and first frame object is set.
                    if (is_object($this->ImagickD->ImagickFirstFrame)) {
                        // if there is first frame object.
                        // get the first frame.
                        $this->getFirstFrame();
                    }

                    // set first frame to prevent Imagick retrieve from last frame and have different dimension.
                    // read more about this here ( https://stackoverflow.com/questions/55176565/imagick-gives-wrong-width-and-height-of-gif )
                    $this->ImagickD->Imagick->setFirstIterator();
                }// endif;

                // keep transparency from gif without any modification.
            }

            // png compression
            $this->ImagickD->png_quality = intval($this->ImagickD->png_quality);
            if ($this->ImagickD->png_quality < 0 || $this->ImagickD->png_quality > 9) {
                $this->ImagickD->png_quality = 0;
            }

            $this->ImagickD->Imagick->setImageFormat('png');
            $this->ImagickD->Imagick->setCompressionQuality(intval($this->ImagickD->png_quality . 5));

            $show_result = $this->ImagickD->Imagick->getImageBlob();
        } elseif ($check_file_ext === 'webp') {
            // if save to webp
            if ($this->ImagickD->source_image_type === IMAGETYPE_GIF) {
                // if source file is gif
                if ($this->ImagickD->source_image_frames > 1) {
                    // if animated gif. set first frame to prevent Imagick retrieve from last frame and have different dimension.
                    // read more about this here ( https://stackoverflow.com/questions/55176565/imagick-gives-wrong-width-and-height-of-gif )
                    $this->ImagickD->Imagick->setFirstIterator();
                }
            }// endif;

            $this->ImagickD->jpg_quality = intval($this->ImagickD->jpg_quality);
            if ($this->ImagickD->jpg_quality < 1) {
                // if quality is less than 1.
                // `setImageCompressionQuality()` support minimum 1, not 0.
                $this->ImagickD->jpg_quality = 1;
            }
            if ($this->ImagickD->jpg_quality > 100) {
                $this->ImagickD->jpg_quality = 100;
            }

            $this->ImagickD->Imagick->setImageFormat('webp');
            $this->ImagickD->Imagick->setImageCompressionQuality($this->ImagickD->jpg_quality);

            $show_result = $this->ImagickD->Imagick->getImageBlob();
        } else {
            $Ims = $this->ImagickD->getStatic();
            $this->ImagickD->status = false;
            $this->ImagickD->statusCode = $Ims::RDIERROR_SHOW_UNSUPPORT;
            $this->ImagickD->status_msg = 'Unable to show this kind of image.';
            unset($Ims);
            return false;
        }

        // clear
        unset($check_file_ext, $file_ext);

        if ($show_result !== false) {
            $this->ImagickD->status = true;
            $this->ImagickD->statusCode = null;
            $this->ImagickD->status_msg = null;
            // Because in PHP GD it is automatically show the image content by omit the file name in `imagexxx()` function without echo command.
            // But in PHP Imagick must echo content that have got from getImageBlob() of Imagick class, then we have to echo it here to make this image class work in the same way.
            echo $show_result;
            return true;
        } else {
            $Ims = $this->ImagickD->getStatic();
            $this->ImagickD->status = false;
            $this->ImagickD->statusCode = $Ims::RDIERROR_SHOW_FAILED;
            $this->ImagickD->status_msg = 'Failed to show the image.';
            unset($Ims);
            return false;
        }
    }// execute


}
