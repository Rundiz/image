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
            }// endif;
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
                // set image page.
                $this->ImagickD->Imagick->setImagePage(0, 0, 0, 0);
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

            $this->ImagickD->Imagick->setImageCompressionQuality($this->ImagickD->jpg_quality);
            $save_result = $this->ImagickD->Imagick->writeImage($file_name);
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
            $this->ImagickD->Imagick->setCompressionQuality(intval($this->ImagickD->png_quality . 5));

            $save_result = $this->ImagickD->Imagick->writeImage($file_name);
        } elseif ($check_file_ext === 'webp') {
            // if save to webp
            if ($this->ImagickD->source_image_type === IMAGETYPE_GIF) {
                // if source image is gif
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

            $this->ImagickD->Imagick->setImageCompressionQuality($this->ImagickD->jpg_quality);
            $save_result = $this->ImagickD->Imagick->writeImage($file_name);
        } else {
            $Ims = $this->ImagickD->getStatic();
            $this->setErrorMessage(sprintf('Unable to save this kind of image. (%s)', $check_file_ext), $Ims::RDIERROR_SAVE_UNSUPPORT);
            unset($Ims);
            return false;
        }

        if (isset($save_result) && $save_result !== false) {
            $this->setStatusSuccess();
            return true;
        } else {
            $Ims = $this->ImagickD->getStatic();
            $this->setErrorMessage('Failed to save the image.', $Ims::RDIERROR_SAVE_FAILED);
            unset($Ims);
            return false;
        }
    }// execute


}
