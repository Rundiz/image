<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image;


/**
 * Calculation class.
 *
 * @since 3.1.5
 */
class Calculation
{


    use Traits\CalculationTrait {
        calculateCounterClockwise as public;
        calculateStartXOfCenter as public;
        calculateWatermarkImageStartXY as public;
        convertAlpha127ToRgba as public;
    }


    /**
     * Calculate new dimension by aspect ratio.
     * 
     * @param number $source_image_width Original image width.
     * @param number $source_image_height Original image height.
     * @param number $new_width New image width.
     * @param number $new_height New image height.
     * @param array $options Associative array:<br>
     *          `master_dim` (string) Master dimension. The value is same as in `Rundiz\Image\AbstractProperties` class. Default is 'auto'.<br>
     *          `allow_resize_larger` (bool) Allow to resize larger than source dimension. The value is same as in `Rundiz\Image\AbstractProperties` class. Default is `false`.<br>
     * @throws \InvalidArgumentException Throw the exception of argument is invalid type.
     */
    public function calculateNewDimensionByRatio(
        $source_image_width, 
        $source_image_height, 
        $new_width,
        $new_height,
        $options = []
    ) {
        if (!is_numeric($source_image_height)) {
            throw new \InvalidArgumentException('The argument $source_image_height must be number.');
        }
        if (!is_numeric($source_image_width)) {
            throw new \InvalidArgumentException('The argument $source_image_width must be number.');
        }
        if (!is_numeric($new_height)) {
            throw new \InvalidArgumentException('The argument $new_height must be number.');
        }
        if (!is_numeric($new_width)) {
            throw new \InvalidArgumentException('The argument $new_width must be number.');
        }

        if (array_key_exists('master_dim', $options) && !is_string($options['master_dim'])) {
            throw new \InvalidArgumentException('The argument `$option[\'master_dim\'] must be string.');
        } elseif (!array_key_exists('master_dim', $options)) {
            $options['master_dim'] = 'auto';
        }
        if (array_key_exists('allow_resize_larger', $options) && !is_bool($options['allow_resize_larger'])) {
            throw new \InvalidArgumentException('The argument `$option[\'allow_resize_larger\'] must be boolean.');
        } elseif (!array_key_exists('allow_resize_larger', $options)) {
            $options['allow_resize_larger'] = false;
        }

        // convert new width, height to integer.
        $new_width = intval($new_width);
        $new_height = intval($new_height);

        // do not allow new width, height equal or less than zero.
        if ($new_height <= 0) {
            $new_height = 1;
        }
        if ($new_width <= 0) {
            $new_width = 1;
        }

        // calculate width, height independently. each of these will be use later, depend on master dimension.
        list($calc_w, $calc_h) = $this->calculateWidthHeightIndependent(
            $source_image_width, 
            $source_image_height, 
            $new_width, 
            $new_height
        );

        // verify master dimension.
        if ('auto' !== $options['master_dim'] && 'width' !== $options['master_dim'] && 'height' !== $options['master_dim']) {
            $options['master_dim'] = 'auto';
        }

        switch ($options['master_dim']) {
            case 'width':
                $new_height = $calc_h;

                if (false === $options['allow_resize_larger']) {
                    // if not allow resize larger.
                    if ($new_width > $source_image_width) {
                        // if new width larger than source image width
                        $new_width = $source_image_width;
                        $new_height = $source_image_height;
                    }
                }
                break;
            case 'height':
                $new_width = $calc_w;

                if (false === $options['allow_resize_larger']) {
                    // if not allow resize larger.
                    if ($new_height > $source_image_height) {
                        // if new height is larger than source image height
                        $new_width = $source_image_width;
                        $new_height = $source_image_height;
                    }
                }
                break;
            case 'auto':
                $source_image_orientation = $this->getSourceImageOrientation($source_image_width, $source_image_height);
                $orig_new_width = $new_width;
                $orig_new_height = $new_height;

                switch ($source_image_orientation) {
                    case 'P':
                        // image orientation portrait
                        $new_width = $calc_w;

                        if (false === $options['allow_resize_larger']) {
                            // if not allow resize larger
                            // determine new image size must not larger than source image size.
                            if ($new_height > $source_image_height && $orig_new_width <= $source_image_width) {
                                // if new height larger than source image height and original new width smaller or equal to source image width
                                $new_width = $orig_new_width;
                                $new_height = $calc_h;
                            } else {
                                if ($new_height > $source_image_height) {
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
                        $new_height = $calc_h;

                        if (false === $options['allow_resize_larger']) {
                            // if not allow resize larger
                            // determine new image size must not larger than source image size.
                            if ($new_width > $source_image_width && $orig_new_height <= $source_image_height) {
                                // if new width larger than source image width and original new height smaller or equal to source image height
                                $new_width = $calc_w;
                                $new_height = $orig_new_height;
                            } else {
                                if ($new_width > $source_image_width) {
                                    $new_width = $source_image_width;
                                    $new_height = $source_image_height;
                                }
                            }
                        }
                        break;
                }// endswitch; source image orientation.

                unset($orig_new_height, $orig_new_width);
                unset($source_image_orientation);
                break;
        }// endswitch; master dimension.

        unset($calc_h, $calc_w);
        return [
            'height' => $new_height,
            'width' => $new_width,
        ];
    }// calculateNewDimensionByRatio


    /**
     * Calculate both width and height by aspect ratio independently.
     * 
     * This will be calculate new height for specified `$new_width` and also calculate new width for specified `$new_height`.
     * 
     * For example: original image dimension is 1920x1080.<br>
     * Enter new width as 800 and calculated new height will be 450.<br>
     * When you use, for master dimension based on width, use calculated new height only (450).<br>
     * So, your new dimension will be 800x450.
     * 
     * The same goes for new height.<br>
     * Enter new height as 800 and calculated new width will be 1422.<br>
     * When you use, for master dimension based on height, use calculated new width only (1422).<br>
     * So, your new dimension will be 1422x800.
     * 
     * @param number $source_image_width Original image width.
     * @param number $source_image_height Original image height.
     * @param number $new_width New image width.
     * @param number $new_height New image height.
     * @return array Return indexed array where index 0 is calculated width (integer), index 1 is calculated height (integer).
     * @throws \InvalidArgumentException Throw the exception of argument is invalid type.
     */
    public function calculateWidthHeightIndependent(
        $source_image_width, 
        $source_image_height, 
        $new_width, 
        $new_height
    ) {
        if (!is_numeric($source_image_height)) {
            throw new \InvalidArgumentException('The argument $source_image_height must be number.');
        }
        if (!is_numeric($source_image_width)) {
            throw new \InvalidArgumentException('The argument $source_image_width must be number.');
        }
        if (!is_numeric($new_height)) {
            throw new \InvalidArgumentException('The argument $new_height must be number.');
        }
        if (!is_numeric($new_width)) {
            throw new \InvalidArgumentException('The argument $new_width must be number.');
        }

        $calc_h = round(($source_image_height/$source_image_width) * $new_width);
        $calc_w = round(($source_image_width/$source_image_height) * $new_height);

        $calc_h = intval($calc_h);
        $calc_w = intval($calc_w);

        return [$calc_w, $calc_h];
    }// calculateWidthHeightIndependent


    /**
     * Get source image orientation.
     * 
     * This method was called by `calculateNewDimensionByRatio()`.
     * 
     * @param number $source_image_width Original image width.
     * @param number $source_image_height Original image height.
     * @return string Return 'S' for square, 'L' for landscape, 'P' for portrait.
     * @throws \InvalidArgumentException Throw the exception of argument is invalid type.
     */
    protected function getSourceImageOrientation($source_image_width, $source_image_height)
    {
        if (!is_numeric($source_image_height)) {
            throw new \InvalidArgumentException('The argument $source_image_height must be number.');
        }
        if (!is_numeric($source_image_width)) {
            throw new \InvalidArgumentException('The argument $source_image_width must be number.');
        }

        if (doubleval($source_image_height) === doubleval($source_image_width)) {
            // square image
            return 'S';
        } elseif ($source_image_height < $source_image_width) {
            // landscape image
            return 'L';
        } else {
            // portrait image
            return 'P';
        }
    }// getSourceImageOrientation


}
