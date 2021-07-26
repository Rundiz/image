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


}
