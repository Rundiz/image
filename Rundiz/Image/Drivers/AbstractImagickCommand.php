<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Drivers;


/**
 * Abstract Imagick command (resize, crop, etc).
 * 
 * @since 3.1.0
 */
class AbstractImagickCommand
{


    /**
     * @var \Rundiz\Image\Drivers\Imagick The Imagick driver class.
     */
    protected $ImagickD;


    public function __construct(\Rundiz\Image\Drivers\Imagick $Imagick) {
        $this->ImagickD = $Imagick;
    }// __construct


}
