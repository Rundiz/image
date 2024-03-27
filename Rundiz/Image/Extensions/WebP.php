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
     * Check that WEBP constant (`IMAGETYPE_WEBP`) is already define, if not then define it.
     * 
     * This constant is not define prior PHP 7.1.
     */
    public function checkWebPConstant()
    {
        if (!defined('IMAGETYPE_WEBP')) {
            define('IMAGETYPE_WEBP', 18);
        }
    }// checkWebPConstant


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

        if (is_array($info) && array_key_exists('ANIMATION', $info) && true === $info['ANIMATION']) {
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
     * Get WebP file info.
     * 
     * @link https://www.php.net/manual/en/function.pack.php unpack format reference.
     * @link https://developers.google.com/speed/webp/docs/riff_container WebP document.
     * @link https://stackoverflow.com/q/61221874/128761 Original question.
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

        fclose($fp);
        unset($fp);

        $header_format = 'A4RIFF/' . // get n string
            'I1FILESIZE/' . // get integer (file size but not actual size)
            'A4WEBP/' . // get n string
            'A4VP/' . // get n string
            'A74chunk';
        $header = unpack($header_format, $data);
        unset($data, $header_format);

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
                // if it is VP8L, I assume that this image will be transparency
                // as described in https://developers.google.com/speed/webp/docs/riff_container#simple_file_format_lossless
                $header['ALPHA'] = true;
            } else {
                $header['ALPHA'] = false;
            }
        }

        unset($header['chunk']);
        return $header;
    }// webPInfo


}
