<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <title>Test Image manipulation class.</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <h1>Rundiz Image manipulation class</h1>
        <h2>Instruction before test</h2>
        <ul>
            <li>Please verify that your php.ini display the errors and report all error level.</li>
            <li>Please make sure that <strong><?php echo realpath('../processed-images'); ?></strong> folder is already exists and has write permission.</li>
            <li>
                All files below must be located in <strong><?php echo realpath('../source-images'); ?></strong> folder.
                <ul>
                    <li>
                        Download photo from <a href="https://pixabay.com/photo-1150319/" target="photostock">this link</a> at 1920&times;1282.<br>
                        Resize to 1920&times;1281 and save as.
                        <ul>
                            <li><strong>source-image.jpg</strong></li>
                        </ul>
                    </li>
                    <li>
                        Convert and save as following file name and extension.<br>
                        (You have to use photo editor program. Not just rename the file extension.)
                        <ul>
                            <li><strong>source-image.avif</strong> (must contain transparent in the image)</li>
                            <li><strong>source-image.gif</strong> (must contain transparent in the image)</li>
                            <li><strong>source-image.png</strong> (must contain transparent in the image)</li>
                            <li><strong>source-image.webp</strong> (must contain transparent in the image)</li>
                            <li><strong>source-image-non-transparent.webp</strong> (must NOT contain transparent in the image)</li>
                        </ul>
                    </li>
                    <li>
                        Copy one file from JPG and rename to .png.
                        <ul>
                            <li><strong>source-image-jpg.png</strong> (This file should be jpg but rename file extension to png.)</li>
                        </ul>
                    </li>
                    <li>
                        Create TXT file and rename to .jpg.
                        <ul>
                            <li><strong>source-image-text.jpg</strong> (This is text file with jpg extension.)</li>
                        </ul>
                    </li>
                    <li>
                        Use animation program (
                        Example <a href="https://forums.getpaint.net/topic/118869-gif-animations-and-images-filetype-plugin-gif-agif-latest-v15-2021-11-16/" target="paintnet_plugingif">1</a>
                        , <a href="https://forums.getpaint.net/topic/119134-webp-animations-and-images-filetype-plugin-webp-awebp-latest-v14-2022-01-24/" target="paintnet_pluginwebp">2</a>
                        ) to open JPG file, resize to 1000&times;667, and add some animation (2 - 3 frames) and save as.
                        <ul>
                            <li><strong>source-image-animated.gif</strong> (This is animation gif. You should create animation in this image.)</li>
                            <li><strong>source-image-animated.webp</strong> (This is animation webp. You should create animation in this image.)</li>
                        </ul>
                    </li>
                    <li>
                        Download photo from <a href="https://www.gstatic.com/webp/gallery3/2_webp_ll.webp" target="google-webp">this link</a> 
                        or from <a href="https://developers.google.com/speed/webp/gallery2" target="google-webp">this page</a> where it is lossless file and save as.
                        <ul>
                            <li><strong>transparent-lossless.webp</strong>.</li>
                        </ul>
                    </li>
                    <li>
                        Download font from <a href="https://fonts.google.com/specimen/Bai+Jamjuree?subset=thai" target="googlefont">this link</a> 
                        or <a href="https://thaifonts.net/fonts/chulanarak-regular" target="thaifont">this link</a> or any font that is supported Thai language.<br>
                        Extract true type font (.ttf extension) and rename to.
                        <ul>
                            <li>
                                <strong>font.ttf</strong>.
                            </li>
                        </ul>
                    </li>
                    <li>
                        Create white empty portrait (tall) image. Dimension is 400&times;800 pixels and save as.
                        <ul>
                            <li><strong>sample-portrait.jpg</strong></li>
                        </ul>
                    </li>
                    <li>
                        Create watermark image files. Dimension is 200&times;50 pixels and save as.
                        <ul>
                            <li><strong>watermark.avif</strong> transparent background, write some text.</li>
                            <li><strong>watermark.gif</strong> transparent background, write some text.</li>
                            <li><strong>watermark.jpg</strong> filled background with color, write some text.</li>
                            <li><strong>watermark.png</strong> transparent background, write some text.</li>
                            <li><strong>watermark.webp</strong> transparent background, write some text.</li>
                        </ul>
                    </li>
                </ul>
            </li> 
        </ul>
        <p><a href="clear-all-processed-images.php">Clear all processed images</a></p>


        <h2>Tests</h2>
        <h3>Data</h3>
        <ul>
            <li><a href="test-show-image-source-data.php">Show image source data</a></li>
        </ul>
        <h3>Native PHP GD functions test</h3>
        <ul>
            <li><a href="native-gd-avif.php">process avif image</a></li>
            <li><a href="native-gd-gif.php">process gif image</a></li>
            <li><a href="native-gd-jpg.php">process jpg image</a></li>
            <li><a href="native-gd-png.php">process png image</a></li>
            <li><a href="native-gd-webp.php">process webp image</a></li>
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
            <li><a href="native-imagick-avif.php">process avif image</a></li>
            <li><a href="native-imagick-gif.php">process gif image</a></li>
            <li><a href="native-imagick-jpg.php">process jpg image</a></li>
            <li><a href="native-imagick-png.php">process png image</a></li>
            <li><a href="native-imagick-webp.php">process webp image</a></li>
            <li><a href="native-imagick-gif-watermark-png.php">gif image with watermark png</a></li>
            <li><a href="native-imagick-animated-gif.php">process animated gif image</a></li>
            <li><a href="native-imagick-animated-webp.php">process animated webp image</a></li>
            <li><a href="native-imagick-avif-compression-tests.php">avif compression tests</a></li>
            <li><a href="native-imagick-jpg-compression-tests.php">jpg compression tests</a></li>
            <li><a href="native-imagick-png-compression-tests.php">png compression tests</a></li>
            <li><a href="native-imagick-webp-compression-tests.php">webp compression tests</a></li>
        </ul>

        <h3>Rundiz Image GD class test</h3>
        <ul>
            <li>save as &amp; show
                <ul>
                    <?php
                    include 'include-image-source.php';
                    $test_data_set2 = $test_data_set +
                        $test_data_falsy;
                    foreach ($test_data_set2 as $imgType => $imgItem) {
                        ?> 
                    <li><a href="rdimage-gd-saveas-and-show.php?imgType=<?=rawurlencode($imgType); ?>"><?=$imgType; ?></a></li>
                        <?php
                    }// endforeach;
                    unset($imgItem, $imgType);
                    ?> 
                </ul>
            </li>
            <li>crop
                <ul>
                    <?php
                    $test_valid_images = $test_data_set2;
                    array_splice($test_valid_images, (count($test_data_set2) - count($test_data_falsy)), count($test_data_falsy), null);
                    foreach ($test_valid_images as $imgType => $imgItem) {
                        ?> 
                    <li><a href="rdimage-gd-crop.php?imgType=<?=rawurlencode($imgType); ?>"><?=$imgType; ?></a></li>
                        <?php
                    }// endforeach;
                    unset($imgItem, $imgType);
                    ?> 
                </ul>
            </li>
            <li><a href="rdimage-gd-resize-not-aspectratio.php">resize by NOT aspect ratio</a></li>
            <li>resize by aspect ratio
                <ul>
                    <?php
                    foreach ($test_valid_images as $imgType => $imgItem) {
                        ?> 
                    <li><a href="rdimage-gd-resize-ratio.php?imgType=<?=rawurlencode($imgType); ?>"><?=$imgType; ?></a></li>
                        <?php
                    }// endforeach;
                    unset($imgItem, $imgType);
                    ?> 
                </ul>
            </li>
            <li>rotate &amp; flip
                <ul>
                    <?php
                    foreach ($test_valid_images as $imgType => $imgItem) {
                        ?> 
                    <li><a href="rdimage-gd-rotate.php?imgType=<?=rawurlencode($imgType); ?>"><?=$imgType; ?></a></li>
                        <?php
                    }// endforeach;
                    unset($imgItem, $imgType);
                    ?> 
                </ul>
            </li>
            <li>watermark image
                <ul>
                    <?php
                    foreach ($test_valid_images as $imgType => $imgItem) {
                        ?> 
                    <li><a href="rdimage-gd-watermark-image.php?imgType=<?=rawurlencode($imgType); ?>"><?=$imgType; ?></a></li>
                        <?php
                    }// endforeach;
                    unset($imgItem, $imgType);
                    ?> 
                </ul>
            </li>
            <li>watermark text
                <ul>
                    <?php
                    foreach ($test_valid_images as $imgType => $imgItem) {
                        ?> 
                    <li><a href="rdimage-gd-watermark-text.php?imgType=<?=rawurlencode($imgType); ?>"><?=$imgType; ?></a></li>
                        <?php
                    }// endforeach;
                    unset($imgItem, $imgType);
                    ?> 
                </ul>
            </li>
            <li><a href="rdimage-gd-multi-process.php">multiple process</a></li>
        </ul>
        <h3>Rundiz Image Imagick class test</h3>
        <?php
        if (!extension_loaded('imagick')) {
            echo '<p class="alert">You don\'t have Imagick extension for PHP installed on this server, please skip these test.</p>';
        }
        ?> 
        <ul>
            <li>save as &amp; show
                <ul>
                    <?php
                    $test_data_set2 = array_slice($test_data_set2, 0, 4, true) +
                        ['GIF Animation' => [
                            'source_image_path' => $source_image_animated_gif,
                        ]] +
                        array_slice($test_data_set2, 0, 5, true) +
                        ['WEBP Animation' => [
                            'source_image_path' => $source_image_animated_webp,
                        ]] +
                        $test_data_falsy;
                    foreach ($test_data_set2 as $imgType => $imgItem) {
                        ?> 
                    <li><a href="rdimage-imagick-saveas-and-show.php?imgType=<?=rawurlencode($imgType); ?>"><?=$imgType; ?></a></li>
                        <?php
                    }// endforeach;
                    unset($imgItem, $imgType);
                    ?> 
                </ul>
            </li>
            <li>crop
                <ul>
                    <?php
                    $test_valid_images = $test_data_set2;
                    array_splice($test_valid_images, (count($test_data_set2) - count($test_data_falsy)), count($test_data_falsy), null);
                    foreach ($test_valid_images as $imgType => $imgItem) {
                        ?> 
                    <li><a href="rdimage-imagick-crop.php?imgType=<?=rawurlencode($imgType); ?>"><?=$imgType; ?></a></li>
                        <?php
                    }// endforeach;
                    unset($imgItem, $imgType);
                    ?> 
                </ul>
            </li>
            <li><a href="rdimage-imagick-resize-not-aspectratio.php">resize by NOT aspect ratio</a></li>
            <li>resize by aspect ratio
                <ul>
                    <?php
                    foreach ($test_valid_images as $imgType => $imgItem) {
                        ?> 
                    <li><a href="rdimage-imagick-resize-ratio.php?imgType=<?=rawurlencode($imgType); ?>"><?=$imgType; ?></a></li>
                        <?php
                    }// endforeach;
                    unset($imgItem, $imgType);
                    ?> 
                </ul>
            </li>
            <li>rotate &amp; flip
                <ul>
                    <?php
                    foreach ($test_valid_images as $imgType => $imgItem) {
                        ?> 
                    <li><a href="rdimage-imagick-rotate.php?imgType=<?=rawurlencode($imgType); ?>"><?=$imgType; ?></a></li>
                        <?php
                    }// endforeach;
                    unset($imgItem, $imgType);
                    ?> 
                </ul>
            </li>
            <li>watermark image
                <ul>
                    <?php
                    foreach ($test_valid_images as $imgType => $imgItem) {
                        ?> 
                    <li><a href="rdimage-imagick-watermark-image.php?imgType=<?=rawurlencode($imgType); ?>"><?=$imgType; ?></a></li>
                        <?php
                    }// endforeach;
                    unset($imgItem, $imgType);
                    ?> 
                </ul>
            </li>
            <li>watermark text
                <ul>
                    <?php
                    foreach ($test_valid_images as $imgType => $imgItem) {
                        ?> 
                    <li><a href="rdimage-imagick-watermark-text.php?imgType=<?=rawurlencode($imgType); ?>"><?=$imgType; ?></a></li>
                        <?php
                    }// endforeach;
                    unset($imgItem, $imgType);
                    ?> 
                </ul>
            </li>
            <li><a href="rdimage-imagick-multi-process.php">multiple process</a></li>
        </ul>

        <h3>Rundiz Image GD &amp; Imagick comparison</h3>
        <p>Some feature comparison</p>
        <ul>
            <li><a href="rdimage-gdimagick-watermark-text.php">watermark text</a></li>
            <li><a href="rdimage-gdimagick-watermark-image.php">watermark image</a></li>
        </ul>
        <?php
        unset($test_data_set2, $test_valid_images);
        ?> 

        <hr>
        <p><small>Fork me on <a href="https://github.com/Rundiz/image" target="github">GitHub</a>.</small></p>
    </body>
</html>