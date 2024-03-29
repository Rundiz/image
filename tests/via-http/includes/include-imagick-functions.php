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


/**
 * Check if image is animated GIF or WEBP.
 * 
 * @param string $image Full path to image.
 * @return mixed Return number of frames. There is 1 frame if it is not animated gif, 2 or more if it is animated gif. Return `false` for otherwise.
 */
function isAnimated($image)
{
    if (!is_file($image)) {
        return false;
    }

    try {
        $Imagick = new \Imagick(realpath($image));
        $number = $Imagick->getNumberImages();
        $Imagick->clear();
        unset($Imagick);
    } catch (\Exception $ex) {
        return false;
    }

    if (is_numeric($number)) {
        return $number;
    }
    return false;
}// isAnimated