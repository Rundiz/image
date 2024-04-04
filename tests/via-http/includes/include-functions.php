<?php
/**
 * Common use functions on any image processing (GD, Imagick).
 */


/**
 * Get auto image file name based on PHP file using.
 *
 * @return string
 */
function autoImageFilename()
{
    $file = 'php' . str_replace(['.'], '', PHP_VERSION) . '-';
    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 20);
    $trace = array_reverse($trace);
    if (isset($trace[0]['file'])) {
        $file .= basename($trace[0]['file'], '.php');
    } else {
        $file .= basename(__FILE__, '.php');
    }
    unset($trace);

    return $file;
}// autoImageFilename


/**
 * Show debugging image data such as dimension.
 *
 * @param string $file Relative path to file that must be able to display in web browser.
 * @param array $options Options:<br>
 *      `dataOnly` (bool) Set to `true` to show data only, `false` to included image thumbnail. Default is `false`.<br>
 *      `imgClass` (string) The HTML `img` tag class to use in stead of default.<br>
 * @return void
 */
function debugImage($file, $options = [])
{
    if (!is_array($options)) {
        $options = [];
    }

    if (!array_key_exists('dataOnly', $options) || !is_bool($options['dataOnly'])) {
        $options['dataOnly'] = false;
    }
    if (!array_key_exists('imgClass', $options) || !is_string($options['imgClass'])) {
        $options['imgClass'] = 'thumbnail';
    }

    if (!is_file($file)) {
        echo '<p class="text-error">File could not be found (' . $file . ').</p>';
        return ;
    }
    echo '<div class="debug-image">' . PHP_EOL;
    if (false === $options['dataOnly']) {
        echo '    <a href="' . $file . '"><img class="' . $options['imgClass'] . '" src="' . $file . '" alt=""></a><br>' . PHP_EOL;
    }
    echo '    File extension: ' . pathinfo($file, PATHINFO_EXTENSION) . '<br>' . PHP_EOL;

    $fileSize = filesize($file);
    echo '    File size: ' . humanFileSize($fileSize) . ' (' . $fileSize . ' bytes)<br>' . PHP_EOL;
    unset($fileSize);

    list($width, $height) = getimagesize($file);
    if (
        (
            !is_numeric($width) 
            || !is_numeric($height) 
        )
    ) {
        // If older version of PHP can't get image width or height. 
        // Tested but PHP <= 7.0.x still doesn't support `getimagesize()` with WEBP.
        $isWebP = strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'webp';
        include_once 'include-rundiz-image.php';
        $WebP = new Rundiz\Image\Extensions\WebP($file);
        $webpInfo = $WebP->webPInfo();
        if (
            is_array($webpInfo) 
            && array_key_exists('HEIGHT', $webpInfo)
            && is_numeric($webpInfo['HEIGHT'])
            && array_key_exists('WIDTH', $webpInfo)
            && is_numeric($webpInfo['WIDTH'])
        ) {
            $height = $webpInfo['HEIGHT'];
            $width = $webpInfo['WIDTH'];
        }

        unset($isWebP, $WebP, $webpInfo);

        if (!is_numeric($height)) {
            $height = 'UNKNOWN';
        }
        if (!is_numeric($width)) {
            $width = 'UNKNOWN';
        }
    }// endif; can't get image width or height.
    echo '    Width&times;Height: ' . $width . '&times;' . $height . ' px<br>' . PHP_EOL;
    unset($height, $width);

    $finfo = new finfo();
    $mime = $finfo->file($file, FILEINFO_MIME_TYPE);
    echo '    Mime type: <code>' . $mime . '</code><br>' . PHP_EOL;
    unset($finfo, $mime);
    echo '</div><!--.debug-image-->' . PHP_EOL;

    unset($options);
} // debugImage


/**
 * Human readable file size.
 *
 * @link https://stackoverflow.com/a/23888858/128761 Original source code.
 * @param int $bytes Number of file size in bytes.
 * @param int $dec
 * @return void
 */
function humanFileSize($bytes, $dec = 2)
{
    $size   = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    if ($factor == 0) {
        $dec = 0;
    }

    return sprintf("%.{$dec}f %s", $bytes / pow(1024, $factor), $size[$factor]);
}// humanFileSize