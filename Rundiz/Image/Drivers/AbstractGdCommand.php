<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Drivers;


/**
 * Abstract GD command (resize, crop, etc).
 * 
 * @since 3.1.0
 */
abstract class AbstractGdCommand
{


    /**
     * @var \Rundiz\Image\Drivers\Gd
     */
    protected $Gd;


    public function __construct(\Rundiz\Image\Drivers\Gd $Gd) {
        $this->Gd = $Gd;
    }// __construct


    /**
     * Set error message and code. This will be set statusXxx properties.
     * 
     * @since 3.1.4
     * @see Rundiz\Image\AbstractImage::setErrorMessage()
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

        $this->Gd->status = false;
        $this->Gd->status_msg = $errorMessage;
        $this->Gd->statusCode = $errorCode;
    }// setErrorMessage


    /**
     * Set status as success and remove error message, error code.
     * 
     * @since 3.1.4
     * @see Rundiz\Image\AbstractImage::setStatusSuccess()
     */
    protected function setStatusSuccess()
    {
        $this->Gd->status = true;
        $this->Gd->status_msg = null;
        $this->Gd->statusCode = null;
    }// setStatusSuccess


}
