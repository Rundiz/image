<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Test Image manipulation class.</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <h1>Rundiz Image manipulation class</h1>
        <h2>Instruction before test</h2>
        <ul>
            <li>Please verify that your php.ini display the errors and report all error level including strict.</li>
            <li>Please make sure that <strong><?php echo realpath('../processed-images'); ?></strong> folder is already exists and has write permission.</li>
            <li>
                Download and create the photo as named below into <strong><?php echo realpath('../source-images'); ?></strong> folder.
                <ul>
                    <li>
                        Download photo from <a href="https://pixabay.com/photo-1150319/" target="photostock">this link</a> at 1920x1282.<br>
                        (You must convert from JPG to GIF and PNG from photo editor program. Not just rename the file extension.)
                        <ul>
                            <li><strong>city-amsterdam.gif</strong> (must contain transparent in the image)</li>
                            <li><strong>city-amsterdam.jpg</strong></li>
                            <li><strong>city-amsterdam.png</strong> (must contain transparent in the image)</li>
                            <li><strong>city-amsterdam.webp</strong> (must contain transparent in the image)</li>
                            <li><strong>city-amsterdam-non-transparent.png</strong> (must NOT contain transparent in the image)</li>
                        </ul>
                        Copy one file from JPG and rename to .png.
                        <ul>
                            <li><strong>city-amsterdam-jpg.png</strong> (This file should be jpg but rename file extension to png.)</li>
                        </ul>
                        Create TXT file and rename to .jpg.
                        <ul>
                            <li><strong>city-amsterdam-text.jpg</strong> (This is text file with jpg extension.)</li>
                        </ul>
                        Use animation program to open JPG file, and add some animation (2 - 3 frames is enough) and save as..
                        <ul>
                            <li><strong>city-amsterdam-animated.gif</strong> (This is animation gif. You should create animation in this image. It is for test with Imagick functions only.)</li>
                            <li><strong>city-amsterdam-animated.webp</strong> (This is animation webp. You should create animation in this image. It is for test with <code>webPInfo()</code> method only.)</li>
                        </ul>
                        Please note that Imagick does not support animated PNG (APNG) nor animated WebP.
                    </li>
                    <li>
                        Download photo from <a href="https://www.gstatic.com/webp/gallery3/2_webp_ll.webp" target="google-webp">this link</a> 
                        or from <a href="https://developers.google.com/speed/webp/gallery2" target="google-webp">this page</a> where it is lossless file
                        <ul>
                            <li>Save as <strong>transparent-lossless.webp</strong>.</li>
                        </ul>
                    </li>
                    <li>
                        Download font from <a href="https://fonts.google.com/specimen/Bai+Jamjuree?subset=thai" target="googlefont">this link</a> 
                        or <a href="https://thaifonts.net/fonts/chulanarak-regular" target="thaifont">this link</a> or any font that is supported Thai language.
                        <ul>
                            <li>
                                Extract <strong>any font name.ttf</strong> and rename it to <strong>font.ttf</strong>.
                            </li>
                        </ul>
                    </li>
                    <li>
                        Create white empty image where it is portrait size (tall) and save as <strong>sample-portrait.jpg</strong>
                    </li>
                    <li>
                        Create watermark image files. Recommended dimension is 200&times;50 pixels.
                        <ul>
                            <li><strong>watermark.gif</strong> transparent background, write some text.</li>
                            <li><strong>watermark.jpg</strong> filled background with color, write some text.</li>
                            <li><strong>watermark.png</strong> transparent background, write some text.</li>
                        </ul>
                    </li>
                </ul>
            </li>
            
            
            
                    
                    
        </ul>
        <p><a href="clear-all-processed-images.php">Clear all processed images</a></p>
        <h2>Tests</h2>
        <h3>Native PHP GD functions test</h3>
        <ul>
            <li><a href="native-gd-jpg.php">process jpg image</a></li>
            <li><a href="native-gd-png.php">process png image</a></li>
            <li><a href="native-gd-gif.php">process gif image</a></li>
            <li><a href="native-gd-gif-watermark-png.php">gif image with watermark png</a></li>
        </ul>
        <h3>Native PHP Imagick class test</h3>
        <?php
        if (!extension_loaded('imagick')) {
            echo '<p class="alert">You don\'t have Imagick extension for PHP installed on this server, please skip these test.</p>';
        }
        ?> 
        <ul>
            <li><a href="native-imagick-info.php">Imagick info</a></li>
            <li><a href="native-imagick-jpg.php">process jpg image</a></li>
            <li><a href="native-imagick-png.php">process png image</a></li>
            <li><a href="native-imagick-gif.php">process gif image</a></li>
            <li><a href="native-imagick-gif-watermark-png.php">gif image with watermark png</a></li>
            <li><a href="native-imagick-animated-gif.php">process animated gif image</a> (this is slower than process non animated gif.)</li>
            <li><a href="native-imagick-jpg-compression-tests.php">jpg compression tests</a></li>
            <li><a href="native-imagick-png-compression-tests.php">png compression tests</a></li>
            <li><a href="native-imagick-webp-compression-tests.php">webp compression tests</a></li>
        </ul>
        <h3>Rundiz Image GD class test</h3>
        <ul>
            <li>basic tests
                <ul>
                    <?php
                    include 'include-image-source.php';
                    $test_data_set2 = array_slice($test_data_set, 0, 2, true) +
                        $test_data_pngnt +
                    array_slice($test_data_set, 2, NULL, true);
                    $test_data_set2 = array_slice($test_data_set2, 0, 5, true) +
                        $test_data_falsy +
                    array_slice($test_data_set2, 5, NULL, true);
                    foreach ($test_data_set2 as $imgType => $imgItem) {
                        ?> 
                    <li><a href="rdimage-gd-basic-tests.php?imgType=<?=rawurlencode($imgType); ?>"><?=$imgType; ?></a></li>
                        <?php
                    }// endforeach;
                    unset($imgItem, $imgType);
                    ?> 
                </ul>
            </li>
            <li><a href="rdimage-gd-crop.php">crop</a></li>
            <li><a href="rdimage-gd-resize-not-aspectratio.php">resize by NOT aspect ratio</a></li>
            <li><a href="rdimage-gd-resize-ratio.php">resize by aspect ratio</a></li>
            <li><a href="rdimage-gd-rotate.php">rotate &amp; flip</a></li>
            <li><a href="rdimage-gd-watermark-image.php">watermark image</a></li>
            <li><a href="rdimage-gd-watermark-text.php">watermark text</a></li>
            <li><a href="rdimage-gd-multi-process.php">multiple process</a></li>
        </ul>
        <h3>Rundiz Image Imagick class test</h3>
        <?php
        if (!extension_loaded('imagick')) {
            echo '<p class="alert">You don\'t have Imagick extension for PHP installed on this server, please skip these test.</p>';
        }
        ?> 
        <ul>
            <li>basic tests
                <ul>
                    <?php
                    $test_data_set2 = array_slice($test_data_set2, 0, 4, true) +
                        ['GIF Animation' => [
                            'source_image_path' => $source_image_animated_gif,
                        ]] +
                    array_slice($test_data_set2, 4, NULL, true);
                    foreach ($test_data_set2 as $imgType => $imgItem) {
                        ?> 
                    <li><a href="rdimage-imagick-basic-tests.php?imgType=<?=rawurlencode($imgType); ?>"><?=$imgType; ?></a></li>
                        <?php
                    }// endforeach;
                    unset($imgItem, $imgType, $test_data_set2);
                    ?> 
                </ul>
            </li>
            <li><a href="rdimage-imagick-crop.php">crop</a></li>
            <li><a href="rdimage-imagick-resize-not-aspectratio.php">resize by NOT aspect ratio</a></li>
            <li><a href="rdimage-imagick-resize-ratio.php">resize by aspect ratio</a></li>
            <li><a href="rdimage-imagick-rotate.php">rotate &amp; flip</a></li>
            <li><a href="rdimage-imagick-watermark-image.php">watermark image</a></li>
            <li><a href="rdimage-imagick-watermark-text.php">watermark text</a></li>
            <li><a href="rdimage-imagick-multi-process.php">multiple process</a></li>
        </ul>
        <h3>Rundiz Image GD &amp; Imagick comparison</h3>
        <p>Some feature comparison</p>
        <ul>
            <li><a href="rdimage-gdimagick-watermark-text.php">watermark text</a></li>
            <li><a href="rdimage-gdimagick-watermark-image.php">watermark image</a></li>
        </ul>

        <hr>
        <p><small>Fork me on <a href="https://github.com/Rundiz/image" target="github">GitHub</a>.</small></p>
    </body>
</html>