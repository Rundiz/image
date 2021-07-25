<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Traits;


/**
 * Calculation trait.
 * 
 * @since 3.1.0
 */
trait CalculationTrait
{


    /**
     * Calculate counter clockwise degree.
     * 
     * Calculate input degree for use with Imagick.<br>
     * In GD 90 degrees is 270 in Imagick. This function help to make it working together very well.
     * 
     * So it will be...<br>
     * input = output<br>
     * 0 = 0<br>
     * 1 = 359<br>
     * 10 = 350<br>
     * 90 = 270<br>
     * 180 = 180<br>
     * 270 = 90<br>
     * 360 = 0
     * 
     * @param int $value Input degrees.
     * @return int Return opposite degrees for use with Imagick.
     */
    protected function calculateCounterClockwise($value)
    {
        if ($value == 0 || $value == 180) {
            return $value;
        } elseif ($value == 360) {
            return 0;
        }

        if ($value < 0 || $value > 360) {
            $value = 90;
        }

        $total_degree = 360;
        $output = intval($total_degree - $value);
        return $output;
    }// calculateCounterClockwise


    /**
     * Calculate variable space.
     * 
     * (min space - (different size * different each))
     * 
     * Example: current size is 20, min size = 10, min space = 0, different each space = 0.3<br>
     * The result will be<br>
     * 20 - 10 = 10 (different size)<br>
     * 0 - (10 * .3) = -3<br>
     * 
     * @param int $currentSize
     * @param int $minSize
     * @param int $minSpace
     * @param float $differentEach
     * @return int
     * @throws \InvalidArgumentException Throw the errors if invalid argument.
     */
    protected function calculateVariableSpace($currentSize, $minSize, $minSpace, $differentEach)
    {
        if (!is_numeric($currentSize) || !is_numeric($minSize) || !is_numeric($minSpace) || !is_numeric($differentEach)) {
            throw new \InvalidArgumentException('The arguments must be number.');
        }

        $return = $minSpace;
        if ($currentSize > $minSize) {
            $sizeDiffMin = ($currentSize - $minSize);
            $return = ($minSpace - ($sizeDiffMin * $differentEach));
        }

        unset($sizeDiffMin);
        return (int) $return;
    }// calculateVariableSpace


    /**
     * Calculate image size by aspect ratio.
     * 
     * @param int $width New width set to calculate.
     * @param int $height New height set to calculate.
     * @return array Return array with 'height' and 'width' in array key and the values are calculated sizes.
     */
    protected function calculateImageSizeRatio($width, $height)
    {
        // convert width, height to integer
        $width = intval($width);
        $height = intval($height);

        if ($height <= 0) {
            $height = 100;
        }
        if ($width <= 0) {
            $width = 100;
        }

        // get and set source (or last modified) image width and height
        $source_image_width = $this->source_image_width;
        if ($this->last_modified_image_width != null) {
            $source_image_width = $this->last_modified_image_width;
        }
        $source_image_height = $this->source_image_height;
        if ($this->last_modified_image_height != null) {
            $source_image_height = $this->last_modified_image_height;
        }

        $source_image_orientation = $this->getSourceImageOrientation();
        // find height and width by aspect ratio.
        $find_h = round(($source_image_height/$source_image_width)*$width);
        $find_w = round(($source_image_width/$source_image_height)*$height);

        $this->verifyMasterDimension();

        switch ($this->master_dim) {
            case 'width':
                $new_width = $width;
                $new_height = $find_h;

                // if not allow resize larger.
                if ($this->allow_resize_larger == false) {
                    // if new width larger than source image width
                    if ($width > $source_image_width) {
                        $new_width = $source_image_width;
                        $new_height = $source_image_height;
                    }
                }
                break;
            case 'height':
                $new_width = $find_w;
                $new_height = $height;

                // if not allow resize larger.
                if ($this->allow_resize_larger == false) {
                    // if new height is larger than source image height
                    if ($height > $source_image_height) {
                        $new_width = $source_image_width;
                        $new_height = $source_image_height;
                    }
                }
                break;
            case 'auto':
            default:
                // master dimension auto.
                switch ($source_image_orientation) {
                    case 'P':
                        // image orientation portrait
                        $new_width = $find_w;
                        $new_height = $height;

                        // if not allow resize larger
                        if ($this->allow_resize_larger == false) {
                            // determine new image size must not larger than source image size.
                            if ($height > $source_image_height && $width <= $source_image_width) {
                                // if new height larger than source image height and width smaller or equal to source image width
                                $new_width = $width;
                                $new_height = $find_h;
                            } else {
                                if ($height > $source_image_height) {
                                    $new_width = $source_image_width;
                                    $new_height = $source_image_height;
                                }
                            }
                        }
                        break;
                    case 'L':
                    // image orientation landscape
                    case 'S':
                    // image orientation square
                    default:
                        // image orientation landscape and square
                        $new_width = $width;
                        $new_height = $find_h;

                        // if not allow resize larger
                        if ($this->allow_resize_larger == false) {
                            // determine new image size must not larger than source image size.
                            if ($width > $source_image_width && $height <= $source_image_height) {
                                // if new width larger than source image width and height smaller or equal to source image height
                                $new_width = $find_w;
                                $new_height = $height;
                            } else {
                                if ($width > $source_image_width) {
                                    $new_width = $source_image_width;
                                    $new_height = $source_image_height;
                                }
                            }
                        }
                        break;
                }
                break;
        }// endswitch;

        unset($find_h, $find_w, $source_image_height, $source_image_orientation, $source_image_width);
        return [
            'height' => $new_height, 
            'width' => $new_width
        ];
    }// calculateImageSizeRatio
	
	
    /**
     * Calculate startX position of center.
     * 
     * With this method, you can find the start x position of center (horizontal) or middle (vertical).
     * 
     * Formular: round((half of canvas) - (half of object))
     * 
     * @param int $objWidth Destination image object size.
     * @param int $canvasWidth Canvas size.
     * @return int Calculated size.
     */
    protected function calculateStartXOfCenter($objWidth, $canvasWidth) 
    {
        if (!is_numeric($objWidth) || !is_numeric($canvasWidth)) {
            return 0;
        }

        return intval(round(($canvasWidth/2)-($objWidth/2)));
    }// calculateStartXOfCenter


    /**
     * Convert alpha number (0 - 127) to rgba value (1.00 - 0.00).
     * 
     * @param int $number Alpha number (0 to 127). 127 is completely transparent.
     * @return string Return RGBA value (1.00 to 0.00). 0.00 is completely transparent.
     */
    protected function convertAlpha127ToRgba($number)
    {
        $alpha_min = 0; // 100%
        $alpha_max = 127; // 0%

        if ($number < $alpha_min) {
            $number = 0;
        } elseif ($number > $alpha_max) {
            $number = 127;
        }

        $find_percent = ($alpha_max - $number) / ($alpha_max / 100);

        unset($alpha_max, $alpha_min);
        return number_format(($find_percent / 100), 2);
    }// convertAlpha127ToRgba


}