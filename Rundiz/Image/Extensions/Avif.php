<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Extensions;


/**
 * AVIF file information.
 *
 * @since 3.1.5
 */
class Avif
{


    /**
     * @var string|null File path.
     */
    protected $file;


    /**
     * WebP file information class.
     *
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
     * Get AVIF info.
     * 
     * @return array Return associative array with keys: `HEIGHT`, `WIDTH`.
     */
    public function avifInfo()
    {
        $output = [];

        if (version_compare(PHP_VERSION, '8.2', '>=') || version_compare(PHP_VERSION, '8.2.0', '>=')) {
            $imagesize = getimagesize($this->file);
            if (is_array($imagesize)) {
                list($width, $height) = $imagesize;
            } else {
                $width = $height = null;
            }

            if (is_numeric($width)) {
                $output['WIDTH'] = $width;
            } else {
                $output['WIDTH'] = null;
            }
            if (is_numeric($height)) {
                $output['HEIGHT'] = $height;
            } else {
                $output['HEIGHT'] = null;
            }
            unset($height, $width);
        } else {
            include_once __DIR__ . '/AvifInfo.php';
            $fh = fopen($this->file, 'rb');

            if (is_resource($fh)) {
                $Parser = new \Rundiz\Avifinfo\Parser($fh);
                $success = $Parser->parse_ftyp() && $Parser->parse_file();
                fclose($fh);

                if ($success) {
                    $features = $Parser->features->primary_item_features;
                    $output['HEIGHT'] = (isset($features['height']) ? $features['height'] : null);
                    $output['WIDTH'] = (isset($features['width']) ? $features['width'] : null);
                    unset($features);
                }
                unset($Parser, $success);
            }

            unset($fh);
        }// endif;

        return $output;
    }// avifInfo


    /**
     * Check that AVIF constants are already define, if not then define it.
     */
    public function checkWebPConstant()
    {
        if (!defined('IMG_AVIF')) {
            // IMG_AVIF available as of PHP 8.10
            define('IMG_AVIF', 256);
        }

        if (!defined('IMAGETYPE_AVIF')) {
            // IMAGETYPE_AVIF available as of PHP 8.1.0
            define('IMAGETYPE_AVIF', 19);
        }
    }// checkWebPConstant


}
