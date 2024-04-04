<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Extensions;


/**
 * Gif file information class.
 * 
 * @since 3.1.0
 */
class Gif
{


    /**
    * Get GIF file info.
    *
    * @link https://www.php.net/manual/en/function.pack.php unpack format reference.
    * @link https://www.file-recovery.com/gif-signature-format.htm Reference.
    * @link https://en.wikipedia.org/wiki/GIF Reference.
    * @link https://www.w3.org/Graphics/GIF/spec-gif89a.txt Reference
    * @link http://www.codediesel.com/php/unpacking-binary-data/ Original source code.
    * @link https://stackoverflow.com/a/415942/128761 Original source code for check animated GIF.
    * @param string $file Full path to image file.
    * @return array|false Return associative array if success, return `false` for otherwise.
    */
    public function gifInfo($file)
    {
        if (!is_file($file)) {
            return false;
        } else {
            $file = realpath($file);
        }

        $fp = fopen($file, 'rb');
        if (!$fp) {
            return false;
        }

        $data = fread($fp, 90);

        $header_format = 'A3GIF/' .
            'A3VERSION/' . // 87a, 89a
            'S1WIDTH/' .
            'S1HEIGHT/'
            ;
        $header = unpack($header_format, $data);
        unset($data, $header_format);

        rewind($fp);
        // check is animated gif. 
        // known problem: this maybe not detected all animated GIF files.
        // https://stackoverflow.com/a/415942/128761 Original source code.
        $count = 0;
        $chunk = false;
        while(!feof($fp) && $count < 2) {
            $chunk = ($chunk ? substr($chunk, -20) : "") . fread($fp, 1024 * 100); //read 100kb at a time
            // \x21 = An Extension Block
            // xF9 = Graphic Control Extension for frame #1
            // \x04 = Number of bytes (4) in the current sub-block
            // .{4} = any 4 byte
            // \x2C = An Image Descriptor
            // \x21 = An Extension Block
            $count += preg_match_all('#\x00?\x21\xF9\x04.{4}\x00[\x2C\x21]#s', $chunk, $matches);
        }// endwhile;
        // end check animated gif.

        fclose($fp);
        unset($chunk, $fp, $matches);

        $header['ANIMATION'] = ($count > 1);

        return $header;
    }// gifInfo


}
