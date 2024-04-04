<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Traits;


/**
 * Image trait.
 * 
 * For use between classes without restriction of `protected`.
 * 
 * @since 3.1.0
 */
trait ImageTrait
{


    /**
     * Get image file data such as width, height, mime type, extension.
     * 
     * This will be use `getimagesize()` function if supported, use Imagick, GD functions as backup.
     * 
     * @since 3.1.0
     * @param string $imagePath Full path to image file.
     * @return array|false Return array:<br>
     *              index 0 Image width.<br>
     *              index 1 Image height.<br>
     *              index 2 Image type constant. See more at https://www.php.net/manual/en/image.constants.php <br>
     *              `mime` key is mime type.<br> 
     *              `ext` key is file extension with dot (.ext).<br>
     *              Other array keys are optional, it can be omit.<br>
     *              Return `false` on failure.<br>
     * @throws \DomainException Throw the errors if it is an image but current PHP version is not supported.
     */
    public function getImageFileData($imagePath)
    {
        if (is_file($imagePath)) {
            $imagePath = realpath($imagePath);
            // get image size and other data using `getimagesize()` function.
            $imgResult = getimagesize($imagePath);
            if (
                    is_array($imgResult) 
                    && array_key_exists(0, $imgResult) 
                    && is_numeric($imgResult[0]) 
                    && array_key_exists(1, $imgResult) 
                    && is_numeric($imgResult[1]) 
                    && array_key_exists(2, $imgResult) 
                    && array_key_exists('mime', $imgResult)
            ) {
                // if image was supported and it really is an image, these keys must exists.
                $imgResult['ext'] = image_type_to_extension($imgResult[2]);
                return $imgResult;
            }
            unset($imgResult);

            // Come to this means, it couldn't get image data. Some older version of PHP can't get image data for example WEBP and PHP <= 7.0.
            if (strtolower(pathinfo($imagePath, PATHINFO_EXTENSION)) === 'webp') {
                // if it is WEBP.
                $WebP = new \Rundiz\Image\Extensions\WebP($imagePath);
                $webpInfo = $WebP->webPInfo();

                if (
                    is_array($webpInfo) 
                    && array_key_exists('HEIGHT', $webpInfo)
                    && is_numeric($webpInfo['HEIGHT'])
                    && array_key_exists('WIDTH', $webpInfo)
                    && is_numeric($webpInfo['WIDTH'])
                ) {
                    $output[0] = $webpInfo['WIDTH'];
                    $output[1] = $webpInfo['HEIGHT'];
                    $output[2] = IMAGETYPE_WEBP;
                    $output['mime'] = 'image/webp';
                    $output['ext'] = '.webp';
                    unset($webpInfo);
                    return $output;
                }

                // come to this means, it can't get any data from file header and chunk.
                if ($WebP->isAnimated()) {
                    // if animated WEBP.
                    throw new \DomainException('Current version of PHP does not support animated WebP.', static::RDIERROR_SRC_WEBP_ANIMATED_NOTSUPPORTED);
                }

                if (isset($webpInfo['ALPHA']) && true === $webpInfo['ALPHA']) {
                    // if transparency WEBP.
                    throw new \DomainException('Current version of PHP does not support alpha transparency WebP.', static::RDIERROR_SRC_WEBP_ALPHA_NOTSUPPORTED);
                }
                unset($WebP, $webpInfo);
            }// endif; it is WEBP.
        }// endif; file exists.

        return false;
    }// getImageFileData


    /**
     * Normalize degree.
     * 
     * If degree is allowed string then it will be return as-is. If degree is number then it will be convert to integer.
     * 
     * @param int|string $degree The degree.
     * @return int|string Return converted degree.
     */
    protected function normalizeDegree($degree)
    {
        $allowed_flip = ['hor', 'vrt', 'horvrt'];
        if (is_numeric($degree)) {
            $degree = intval($degree);
        } elseif (!is_numeric($degree) && !in_array($degree, $allowed_flip)) {
            $degree = 90;
        }

        unset($allowed_flip);
        return $degree;
    }// normalizeDegree


    /**
     * Convert width, height to integer and check if it is less than 0 then set to minimum value.
     * 
     * @param int $width Width
     * @param int $height Height
     * @param int|false $minWidth Minimum that width must not less than 0. Set to `false` to not check it. Default is 100.
     * @param int|false $minHeight Minimum that height must not less than 0. Set to `false` to not check it. Default is 100.
     * @return array Return 2D array where first index is width, second index is height.
     */
    protected function normalizeWidthHeight($width, $height, $minWidth = 100, $minHeight = 100)
    {
        $height = intval($height);
        $width = intval($width);

        // width and height must larger than 0
        if (is_int($minHeight) && $height <= 0) {
            $height = $minHeight;
        }
        if (is_int($minWidth) && $width <= 0) {
            $width = $minWidth;
        }

        return [$width, $height];
    }// normalizeWidthHeight


}
