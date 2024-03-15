<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image;


/**
 * File system class.
 * 
 * @since 3.1.0
 */
class FileSystem
{


    /**
     * Get file extension only.
     * 
     * It will be also replace jpeg to jpg.
     * 
     * @param string $filename File name or the string that contain extension
     * @return string Return file extension only without dot.
     */
    public function getFileExtension($filename)
    {
        if (empty($filename) || !is_string($filename)) {
            return '';
        }

        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $ext = str_ireplace('jpeg', 'jpg', $ext);
        return $ext;
    }// getFileExtension


    /**
     * Get file real path of the parameter `$filename`.
     * 
     * In case that the directory that is parent of file name does not exists, you have to create it before calling this.
     * 
     * @param string $filename File path relative or real path.
     * @return string Return real path to file.
     */
    public function getFileRealpath($filename)
    {
        if (!is_string($filename)) {
            return '';
        }

        $filePathExp = explode('/', str_ireplace('\\', '/', $filename));
        $fileNameOnly = $filePathExp[count($filePathExp)-1];
        unset($filePathExp[count($filePathExp)-1]);

        // get only folder that contain file name. example /var/www/html/image.jpg then it will be /var/www/html
        $fileDir = implode(DIRECTORY_SEPARATOR, $filePathExp);
        unset($filePathExp);

        return (string) realpath($fileDir) . DIRECTORY_SEPARATOR . $fileNameOnly;
    }// getFileRealpath


}
