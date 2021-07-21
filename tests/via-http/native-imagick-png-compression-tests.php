<?php
$sourceImageFile = '../source-images/city-amsterdam.jpg';
$processImagesFolder = '../processed-images/';
$processImagesFullpath = realpath($processImagesFolder) . DIRECTORY_SEPARATOR;


echo '<p>';
echo 'Original <a href="' . $sourceImageFile . '">image</a><br>';
clearstatcache();
echo 'File size: ' . filesize($sourceImageFile) . ' bytes';
echo '</p>' . PHP_EOL;

echo '<hr>' . PHP_EOL;

$Imagick = new Imagick(realpath($sourceImageFile));

for ($compression = 0; $compression <= 9; $compression++) {
    echo 'Compression: ' . $compression . '.';
    $ImagickCloned = clone $Imagick;
    $ImagickCloned->setCompressionQuality(intval($compression . 5));
    $ImagickCloned->setImageFormat('png');
    $saveImgLink = basename(__FILE__, '.php') . '-compression' . $compression . '.png';
    $ImagickCloned->writeImage('png:' . $processImagesFullpath . $saveImgLink);
    $ImagickCloned->clear();
    unset($ImagickCloned);
    clearstatcache();
    echo ' ';
    echo 'File size: ' . filesize($processImagesFullpath . $saveImgLink) . ' bytes.<br>' . PHP_EOL;
}// endfor;
unset($compression);

//echo '<hr>' . PHP_EOL;
//echo 'Compress all types tests<br>' . PHP_EOL;

//compressAllTypes($Imagick, $processImagesFullpath . 'compress-all-types-tests');

$Imagick->clear();
unset($Imagick);


/**
 * Compression all types.
 * 
 * @link https://stackoverflow.com/a/26997997/128761 Original source code.
 * @param Imagick $imagick
 * @param type $filename
 */
function compressAllTypes(Imagick $imagick, $filename) {
    //     0's digit:
    //
    //        0 or omitted: Use Z_HUFFMAN_ONLY strategy with the
    //           zlib default compression level
    //
    //        1-9: the zlib compression level
    //
    //     1's digit:
    //
    //        0-4: the PNG filter method
    //
    //        5:   libpng adaptive filtering if compression level > 5
    //             libpng filter type "none" if compression level <= 5
    //or if image is grayscale or palette
    //
    //        6:   libpng adaptive filtering
    //
    //        7:   "LOCO" filtering (intrapixel differing) if writing
    //a MNG, otherwise "none".  Did not work in IM-6.7.0-9
    //and earlier because of a missing "else".
    //
    //        8:   Z_RLE strategy (or Z_HUFFMAN_ONLY if quality < 10), adaptive
    //             filtering. Unused prior to IM-6.7.0-10, was same as 6
    //
    //        9:   Z_RLE strategy (or Z_HUFFMAN_ONLY if quality < 10), no PNG filters
    //             Unused prior to IM-6.7.0-10, was same as 6
    for ($compression = 0; $compression <= 9; $compression++) {
        echo "Compression $compression <br>\n";
        for ($filter = 0; $filter <= 9; $filter++) {
            echo " &nbsp; Filter $filter";
            $output = clone $imagick;
            $output->setImageFormat('png');
            //$output->setOption('png:format', 'png8');
            $compressionType = intval($compression . $filter);
            $output->stripImage();
            $output->setCompressionQuality($compressionType);
            $outputName = $filename . 'compression' . $compression . 'filter' . $filter . '.png';
            $output->writeImage($outputName);
            clearstatcache();
            echo ' - ';
            echo 'File size: ' . filesize($outputName) . ' bytes';
            echo '<br>' . PHP_EOL;
        }
    }
}