<?php
/**
 * PHP Image manipulation class.
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 */


namespace Rundiz\Image;

use Rundiz\Image\ImageInterface;

/**
 * Abstract class of Image class.
 *
 * @since 3.0
 * Renamed from ImageAbstractClass since 3.1.0
 */
abstract class AbstractImage extends AbstractProperties implements ImageInterface
{


    use Traits\CalculationTrait;


    use Traits\ImageTrait;


    /**
     * Class constructor.
     * 
     * @param string $source_image_path Path to source image file.
     * @return bool Return true on success, false on failed. Call to `statusCode` or `status_msg` property to see the details on failure.
     */
    public function __construct($source_image_path)
    {
        return $this->buildSourceImageData($source_image_path);
    }// __construct


    /**
     * Class de-constructor.
     */
    public function __destruct()
    {
        $this->clear();
    }// __destruct


    /**
     * Magic get
     * @param string $name
     */
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }
    }// __get


    /**
     * Magic set
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            $this->{$name} = $value;
        }
    }// __set


    /**
     * Build source image data
     * 
     * @param string $source_image_path Path to source image file.
     * @return bool Return true on success, false on failed. Call to `statusCode` or `status_msg` property to see the details on failure.
     */
    protected function buildSourceImageData($source_image_path)
    {
        if (!is_string($source_image_path)) {
            $source_image_path = (string) $source_image_path;
        }

        $WebP = new Extensions\WebP();
        $WebP->checkWebPConstant();
        unset($WebP);

        if (is_file($source_image_path)) {
            $source_image_path = realpath($source_image_path);
            try {
                $image_data = $this->getImageFileData($source_image_path);
            } catch (\Exception $ex) {
                $this->setErrorMessage($ex->getMessage(), $ex->getCode());
                return false;
            }

            if (false !== $image_data && is_array($image_data) && !empty($image_data)) {
                $this->source_image_path = $source_image_path;
                $this->source_image_width = $image_data[0];
                $this->source_image_height = $image_data[1];
                $this->source_image_type = $image_data[2];
                $this->source_image_mime = $image_data['mime'];
                $this->source_image_ext = str_ireplace('jpeg', 'jpg', image_type_to_extension($image_data[2]));
                $this->source_image_data = $image_data;
                unset($image_data);

                $this->setStatusSuccess();
                return true;
            } else {
                unset($image_data);

                $this->setErrorMessage('Unable to get image data. This file is not an image.', static::RDIERROR_SRC_NOTIMAGE);
                return false;
            }
        } else {
            $this->setErrorMessage('Source image is not exists.', static::RDIERROR_SRC_NOTEXISTS);
            return false;
        }
    }// buildSourceImageData


    /**
     * {@inheritDoc}
     * 
     * This method will be reset all properties that is commonly use but not reset specific properties for GD, Imagick.<br>
     * The properties that will be clear is based on `ImageInterface` interface that was described.
     */
    public function clear()
    {
        // don't reset setting properties to let it continuous run.

        // reset result properties.
        $this->status = false;
        $this->statusCode = null;
        $this->status_msg = null;

        // reset working process properties.
        $this->last_modified_image_height = null;
        $this->last_modified_image_width = null;
        $this->destination_image_height = null;
        $this->destination_image_width = null;
        $this->watermark_image_type = null;
        $this->watermark_image_width = null;
        $this->watermark_image_height = null;
    }// clear


    /**
     * {@inheritDoc}
     */
    public function getImageSize()
    {
        return [
            'height' => $this->source_image_height,
            'width' => $this->source_image_width,
        ];
    }// getImageSize


    /**
     * Get source image orientation.<br>
     * This method called by calculateImageSizeRatio().
     * 
     * @return string Return S for square, L for landscape, P for portrait.
     */
    protected function getSourceImageOrientation()
    {
        if ($this->source_image_height == $this->source_image_width) {
            // square image
            return 'S';
        } elseif ($this->source_image_height < $this->source_image_width) {
            // landscape image
            return 'L';
        } else {
            // portrait image
            return 'P';
        }
    }// getSourceImageOrientation


    /**
     * Verify that is class setup properly.
     * If image source was not found then it will not setup properly.
     * 
     * @return bool Return true on success, return false on failed.
     */
    protected function isClassSetup()
    {
        if ($this->source_image_path == null) {
            return false;
        }

        return true;
    }// isClassSetup


    /**
     * Check is previous operation contain error?
     * 
     * @return bool Return true if there is some error, false if there is not.
     */
    protected function isPreviousError()
    {
        if ($this->status == false && ($this->statusCode != null || $this->status_msg != null)) {
            return true;
        }
        return false;
    }// isPreviousError


    /**
     * {@inheritDoc}
     */
    public function resize($width, $height)
    {
        if (false === $this->isClassSetup()) {
            return false;
        }

        $sizes = $this->calculateImageSizeRatio($width, $height);

        if (
            !is_array($sizes) || 
            (
                is_array($sizes) && 
                (!array_key_exists('height', $sizes) || !array_key_exists('width', $sizes))
            )
        ) {
            $this->setErrorMessage('Unable to calculate sizes, please try to calculate on your own and call to resizeNoRatio() instead.', static::RDIERROR_CALCULATEFAILED_USE_RESIZENORATIO);
            return false;
        }

        return $this->resizeNoRatio($sizes['width'], $sizes['height']);
    }// resize


    /**
     * Set error message and code. This will be set statusXxx properties.
     * 
     * @since 3.1.4
     * @param string $errorMessage The error message.
     * @param int $errorCode The error code, usually it is class's constant.
     * @throws \InvalidArgumentException Throw the exception if argument type is invalid.
     */
    protected function setErrorMessage($errorMessage, $errorCode)
    {
        if (!is_string($errorMessage)) {
            throw new \InvalidArgumentException('The argument $errorMessage must be string.');
        }
        if (!is_int($errorCode)) {
            throw new \InvalidArgumentException('The argument $errorCode must be integer.');
        }

        $this->status = false;
        $this->status_msg = $errorMessage;
        $this->statusCode = $errorCode;
    }// setErrorMessage


    /**
     * Set status as success and remove error message, error code.
     * 
     * @since 3.1.4
     */
    protected function setStatusSuccess()
    {
        $this->status = true;
        $this->status_msg = null;
        $this->statusCode = null;
    }// setStatusSuccess


    /**
     * Verify master dimension value must be correctly.<br>
     * This method called by calculateImageSizeRatio().
     */
    protected function verifyMasterDimension() 
    {
       $this->master_dim = strtolower($this->master_dim);

       if ($this->master_dim != 'auto' && $this->master_dim != 'width' && $this->master_dim != 'height') {
           $this->master_dim = 'auto';
       }
    }// verifyMasterDimension


}
