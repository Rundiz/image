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
     * Get WebP file info.
     * 
     * @link https://www.php.net/manual/en/function.pack.php unpack format reference.
     * @link https://developers.google.com/speed/webp/docs/riff_container WebP document.
     * @link https://stackoverflow.com/q/61221874/128761 Original question.
     * @param string $file Full path to image file.
     * @return array|false Return associative array if success, return `false` for otherwise.
     */
    public function webPInfo($file)
    {
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
