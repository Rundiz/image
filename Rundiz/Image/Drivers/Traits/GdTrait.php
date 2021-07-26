<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Drivers\Traits;


/**
 * GD trait.
 * 
 * @since 3.1.0
 */
trait GdTrait
{


    /**
     * Check if image variable is resource of GD or is object of `\GDImage` (PHP 8.0) or not.
     *
     * @since 3.0.2 Moved from `\Rundiz\Image\Drivers\Gd`
     * @param mixed $image
     * @return bool Return `true` if it is resource or instance of `\GDImage`, return `false` if it is not.
     */
    protected function isResourceOrGDObject($image)
    {
        return (
            (is_resource($image) && get_resource_type($image) === 'gd') ||
            (is_object($image) && $image instanceof \GDImage)
        );
    }// isResourceOrGDObject


}
