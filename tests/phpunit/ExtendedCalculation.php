<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Tests;


class ExtendedCalculation extends \Rundiz\Image\Calculation
{


    /**
     * {@inheritDoc}
     */
    public function calculateCounterClockwise($value)
    {
        return parent::calculateCounterClockwise($value);
    }// calculateCounterClockwise


    /**
     * {@inheritDoc}
     */
    public function getSourceImageOrientation($source_image_width, $source_image_height)
    {
        return parent::getSourceImageOrientation($source_image_width, $source_image_height);
    }// getSourceImageOrientation


}
