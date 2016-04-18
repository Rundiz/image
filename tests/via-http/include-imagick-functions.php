<?php
/**
 * Calculate counter clockwise degree.
 * In GD 90 degree is 270 in Imagick. This function help to make it working together very well.
 * 
 * @param integer $value Degrees.
 * @return integer Return opposite degrees.
 */
function calculateCounterClockwise($value)
{
    if ($value == 0 || $value == 180) {
        return $value;
    }
    if ($value < 0 || $value > 360) {
        $value = 90;
    }

    $total_degree = 360;
    $output = intval($total_degree-$value);
    return $output;
}// calculateCounterClockwise