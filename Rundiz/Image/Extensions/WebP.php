<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Extensions;


/**
 * WebP file information class.
 * 
 * @since 3.1.0
 */
class WebP
{


    /**
     * @since 3.1.4
     * @var string|null File path.
     */
    protected $file;


    /**
     * WebP file information class.
     *
     * @since 3.1.4
     * @param string $file Path to WEBP file.
     */
    public function __construct($file = '')
    {
        if (is_string($file) && !empty($file)) {
            $this->file = $file;
        } else {
            $this->file = null;
        }
    }// __construct


    /**
     * Check that WEBP constants are already define, if not then define it.
     */
    public function checkWebPConstant()
    {
        if (!defined('IMG_WEBP')) {
            // IMG_WEBP available as of PHP 7.0.10
            define('IMG_WEBP', 32);
        }

        if (!defined('IMAGETYPE_WEBP')) {
            // IMAGETYPE_WEBP available as of PHP 7.1.0
            define('IMAGETYPE_WEBP', 18);
        }
    }// checkWebPConstant


    /**
     * Check if WEBP file that was specified is animated or not.
     * 
     * @since 3.1.4
     * @return bool Return `true` if yes, `false` if something else.
     */
    public function isAnimated()
    {
        $info = $this->webPInfo();
        if (!is_array($info)) {
            // If not WEBP.
            unset($info);
            return false;
        }

        return (is_array($info) && array_key_exists('ANIMATION', $info) && true === $info['ANIMATION']);
    }// isAnimated


    /**
     * Check if GD supported current WEBP file.
     *
     * @since 3.1.4
     * @return boolean Return `true` if yes, `false` if no.
     */
    public function isGDSupported()
    {
        $info = $this->webPInfo();
        if (!is_array($info)) {
            // If not WEBP.
            unset($info);
            return false;
        }

        if ($this->isAnimated()) {
            // If animated WEBP.
            // Currently there is no supported for animated WEBP (last checked PHP 8.3).
            // @link https://www.php.net/manual/en/function.imagecreatefromwebp.php Reference for can't read animated WEBP.
            unset($info);
            return false;
        }

        if (function_exists('imagecreatefromwebp')) {
            // If there is function supported.
            if (version_compare(PHP_VERSION, '7.0', '>=')) {
                // If PHP 7.0 or newer.
                // Yes!
                unset($info);
                return true;
            } else {
                // If PHP is older than 7.0.
                // Notice:
                // PHP <= 5.6 does not supported transparency WEBP and cause error "WebP decode: fail to decode input data".
                // PHP < 5.6 does not fully supported non-transparency WEBP because image will becomes green or incorrect color.
                $isTransparent = (is_array($info) && array_key_exists('ALPHA', $info) && true === $info['ALPHA']);
                if (!$isTransparent) {
                    // If image does not contain transparency.
                    // Yes!
                    unset($info, $isTransparent);
                    return true;
                }
                unset($isTransparent);
            }
        }// endif; function_exists().

        unset($info);
        return false;
    }// isGDSupported


    /**
     * Check if Imagick (and ImageMagick) supported animated WEBP.
     * 
     * This test is not depend on WEBP file specified.
     * 
     * @since 3.1.4
     * @return bool Return `true` if it is already supported. Return `false` for otherwise.
     */
    public function isImagickSupportedAnimated()
    {
        $immVA = \Imagick::getVersion();// get ImageMagick version array.
        if (array_key_exists('versionString', $immVA)) {
            preg_match('/ImageMagick ([0-9]+\.[0-9]+\.[0-9]+)/', $immVA['versionString'], $matches);
            if (isset($matches[1]) && version_compare($matches[1], '7.0.8.68', '<')) {
                // if using older version of ImageMagick that doesn't supported animated WEBP.
                unset($immVA, $matches);
                return false;
            } else {
                unset($immVA, $matches);
                return true;
            }
        }
        unset($immVA);
    }// isImagickSupportedAnimated


    /**
     * Get WebP file info.
     * 
     * @link https://www.php.net/manual/en/function.pack.php unpack format reference.
     * @link https://developers.google.com/speed/webp/docs/riff_container WebP document.
     * @link https://stackoverflow.com/q/61221874/128761 Original question.
     * @link https://developer.wordpress.org/reference/functions/wp_get_webp_info/ Original source code for get WEBP width and height (in case `getimagesize()` did not work on old PHP). By WordPress.
     * @param string $file Full path to image file. You can omit this argument and use one in class constructor instead.
     * @return array|false Return associative array if success, return `false` for otherwise.
     */
    public function webPInfo($file = '')
    {
        if (!is_string($file) || (is_string($file) && empty($file))) {
            $file = $this->file;
        }

        if (!is_file($file)) {
            // if file was not found.
            return false;
        } else {
            $file = realpath($file);
        }

        $fp = fopen($file, 'rb');
        if (!$fp) {
            // if could not open file.
            return false;
        }

        $data = fread($fp, 90);

        $header_format = 'A4RIFF/' . // get n string
            'I1FILESIZE/' . // get integer (file size but not actual size)
            'A4WEBP/' . // get n string
            'A4VP/' . // get n string
            'A74chunk';
        $header = unpack($header_format, $data);
        unset($header_format);

        // the conditions below means this file is not webp image.
        if (!isset($header['RIFF']) || strtoupper($header['RIFF']) !== 'RIFF') {
            return false;
        }
        if (!isset($header['WEBP']) || strtoupper($header['WEBP']) !== 'WEBP') {
            return false;
        }
        if (!isset($header['VP']) || strpos(strtoupper($header['VP']), 'VP8') === false) {
            return false;
        }

        // check for animation.
        if (
            strpos(strtoupper($header['chunk']), 'ANIM') !== false || 
            strpos(strtoupper($header['chunk']), 'ANMF') !== false
        ) {
            $header['ANIMATION'] = true;
        } else {
            $header['ANIMATION'] = false;
        }

        // check for transparent.
        if (strpos(strtoupper($header['chunk']), 'ALPH') !== false) {
            $header['ALPHA'] = true;
        } else {
            if (strpos(strtoupper($header['VP']), 'VP8L') !== false) {
                // if it is VP8L.
                // @link https://developers.google.com/speed/webp/docs/riff_container#simple_file_format_lossless Reference.
                $header['ALPHA'] = (bool) (!!(ord($data[24]) & 0x00000010));
            } elseif (strpos(strtoupper($header['VP']), 'VP8X') !== false) {
                // if it is VP8X.
                // @link https://developers.google.com/speed/webp/docs/riff_container#extended_file_format Reference.
                // Some animated file may seems to not having any alpha transparency part but in fact, it is in some frame(s) of that animation. 
                // You can test by extract each animation frame to files with `$Imagick->writeImages('name.webp', false);`.
                $header['ALPHA'] = (bool) (!!(ord($data[20]) & 0x00000010));
            } else {
                $header['ALPHA'] = false;
            }
        }

        // get width & height.
        // @link https://developer.wordpress.org/reference/functions/wp_get_webp_info/ Original source code.
        if (strtoupper($header['VP']) === 'VP8') {
            $parts = unpack('v2', substr($data, 26, 4));
            $header['WIDTH'] = (int) ($parts[1] & 0x3FFF);
            $header['HEIGHT'] = (int) ($parts[2] & 0x3FFF);
        } elseif (strtoupper($header['VP']) === 'VP8L') {
            $parts = unpack('C4', substr($data, 21, 4));
            $header['WIDTH'] = (int) (($parts[1] | (($parts[2] & 0x3F) << 8)) + 1);
            $header['HEIGHT'] = (int) (((($parts[2] & 0xC0) >> 6) | ($parts[3] << 2) | (($parts[4] & 0x03) << 10)) + 1);
        } elseif (strtoupper($header['VP']) === 'VP8X') {
            // Pad 24-bit int.
            $width = unpack('V', substr($data, 24, 3) . "\x00");
            $header['WIDTH'] = (int) ($width[1] & 0xFFFFFF) + 1;
            // Pad 24-bit int.
            $height = unpack('V', substr($data, 27, 3) . "\x00");
            $header['HEIGHT'] = (int) ($height[1] & 0xFFFFFF) + 1;
        }
        unset($height, $parts, $width);

        fclose($fp);
        unset($data, $fp, $header['chunk']);
        return $header;
    }// webPInfo


}
