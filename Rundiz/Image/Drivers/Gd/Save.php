<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Drivers\Gd;


/**
 * Save command class.
 * 
 * @since 3.1.0
 */
class Save extends \Rundiz\Image\Drivers\AbstractGdCommand
{


    public function execute($file_name)
    {
        $FS = new \Rundiz\Image\FileSystem();
        $file_name = $FS->getFileRealpath($file_name);
        $check_file_ext = strtolower($FS->getFileExtension($file_name));
        unset($FS);

        // save to file. each image types use different ways to save.
        if ($check_file_ext == 'gif') {
            if ($this->Gd->source_image_type === IMAGETYPE_PNG) {
                // source image is png file. convert transparency png to white before save.
                $temp_image_object = imagecreatetruecolor($this->Gd->destination_image_width, $this->Gd->destination_image_height);
                $white = imagecolorallocate($temp_image_object, 255, 255, 255);
                imagefill($temp_image_object, 0, 0, $white);
                imagecopy($temp_image_object, $this->Gd->destination_image_object, 0, 0, 0, 0, $this->Gd->destination_image_width, $this->Gd->destination_image_height);
                $this->Gd->destination_image_object = $temp_image_object;
                unset($temp_image_object, $white);
            }

            $save_result = imagegif($this->Gd->destination_image_object, $file_name);
        } elseif ($check_file_ext == 'jpg') {
            if ($this->Gd->source_image_type === IMAGETYPE_PNG || $this->Gd->source_image_type === IMAGETYPE_GIF) {
                // source image is png or gif file. convert transparency png to white before save.
                $temp_image_object = imagecreatetruecolor($this->Gd->destination_image_width, $this->Gd->destination_image_height);
                $white = imagecolorallocate($temp_image_object, 255, 255, 255);
                imagefill($temp_image_object, 0, 0, $white);
                imagecopy($temp_image_object, $this->Gd->destination_image_object, 0, 0, 0, 0, $this->Gd->destination_image_width, $this->Gd->destination_image_height);
                $this->Gd->destination_image_object = $temp_image_object;
                unset($temp_image_object, $white);
            }

            $this->Gd->jpg_quality = intval($this->Gd->jpg_quality);
            if ($this->Gd->jpg_quality < 0 || $this->Gd->jpg_quality > 100) {
                $this->Gd->jpg_quality = 100;
            }

            $save_result = imagejpeg($this->Gd->destination_image_object, $file_name, $this->Gd->jpg_quality);
        } elseif ($check_file_ext == 'png') {
            if ($this->Gd->source_image_type === IMAGETYPE_GIF) {
                // source image is gif file. convert transparency gif to white before save.
                // source transparent png to gif have no problem but source transparent gif to png it always left transparency. it must be filled.
                $temp_image_object = imagecreatetruecolor($this->Gd->destination_image_width, $this->Gd->destination_image_height);
                $white = imagecolorallocate($temp_image_object, 255, 255, 255);
                imagefill($temp_image_object, 0, 0, $white);
                imagecopy($temp_image_object, $this->Gd->destination_image_object, 0, 0, 0, 0, $this->Gd->destination_image_width, $this->Gd->destination_image_height);
                $this->Gd->destination_image_object = $temp_image_object;
                unset($temp_image_object, $white);
            }

            $this->Gd->png_quality = intval($this->Gd->png_quality);
            if ($this->Gd->png_quality < 0 || $this->Gd->png_quality > 9) {
                $this->Gd->png_quality = 0;
            }

            $save_result = imagepng($this->Gd->destination_image_object, $file_name, $this->Gd->png_quality);
        } else {
            $this->Gd->status = false;
            $this->Gd->status_msg = sprintf('Unable to save this kind of image. (%s)', $check_file_ext);
            return false;
        }

        // clear
        unset($check_file_ext, $file_ext);

        if (isset($save_result) && $save_result !== false) {
            $this->Gd->status = true;
            $this->Gd->status_msg = null;
            return true;
        } else {
            $this->Gd->status = false;
            $this->Gd->status_msg = 'Failed to save the image.';
            return false;
        }
    }// execute


}
