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
                Download photo from <a href="https://pixabay.com/photo-1150319/" target="photostock">this link</a> at 1920x1281 and save it to <?php echo realpath('../source-images'); ?> folder with these name and extensions.<br>
                (You must convert from JPG to GIF and PNG from photo editor program. Not just rename the file extension.)
                <ul>
                    <li>city-amsterdam.gif (must contain transparent in the image)</li>
                    <li>city-amsterdam.jpg</li>
                    <li>city-amsterdam.png (must contain transparent in the image)</li>
                </ul>
                Copy one file from JPG and rename to .png.
                <ul>
                    <li>city-amsterdam-jpg.png (This file should be jpg but rename file extension to png.)</li>
                </ul>
                Create TXT file and rename to .jpg.
                <ul>
                    <li>city-amsterdam-text.jpg (This is text file with jpg extension.)</li>
                </ul>
                Use animation program to open JPG file, and add some animation (2 - 3 frames is enough) and save as..
                <ul>
                    <li>city-amsterdam-animated.gif (This is animation gif. You should create animation in this image. It is for test with Imagick functions only.)</li>
                    <li>city-amsterdam-animated.webp (This is animation webp. You should create animation in this image. It is for test with <code>isAnimatedWebP()</code> method only.)</li>
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
        <h3>Native PHP Imagick functions test</h3>
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
            <li><a href="native-imagick-png-compression-tests.php">png compression tests</a></li>
        </ul>
        <h3>Rundiz GD Image class test</h3>
        <ul>
            <li><a href="rdimage-gd-basic-tests.php">basic tests</a></li>
            <li><a href="rdimage-gd-resize-ratio.php">resize by aspect ratio</a></li>
            <li><a href="rdimage-gd-rotate.php">rotate</a></li>
            <li><a href="rdimage-gd-flip.php">flip</a></li>
            <li><a href="rdimage-gd-multi-process.php">multiple process</a></li>
            <li><a href="rdimage-gd-watermark-image.php">watermark image</a> (can be very slow, please wait)</li>
            <li><a href="rdimage-gd-watermark-text.php">watermark text</a></li>
            <li><a href="rdimage-gd-transparent-nontransparent-gif.php">transparent &amp; non-transparent gif</a></li>
        </ul>
        <h3>Rundiz Imagick Image class test</h3>
        <?php
        if (!extension_loaded('imagick')) {
            echo '<p class="alert">You don\'t have Imagick extension for PHP installed on this server, please skip these test.</p>';
        }
        ?> 
        <ul>
            <li><a href="rdimage-imagick-basic-tests.php">basic tests</a></li>
            <li><a href="rdimage-imagick-resize-ratio.php">resize by aspect ratio</a></li>
            <li><a href="rdimage-imagick-rotate.php">rotate</a></li>
            <li><a href="rdimage-imagick-flip.php">flip</a></li>
            <li><a href="rdimage-imagick-multi-process.php">multiple process</a></li>
            <li><a href="rdimage-imagick-watermark-image.php">watermark image</a></li>
            <li><a href="rdimage-imagick-watermark-text.php">watermark text</a></li>
            <li><a href="rdimage-imagick-animate-gif-watermark-text.php">animate gif and watermark text</a></li>
            <li><a href="rdimage-imagick-transparent-nontransparent-gif.php">transparent &amp; non-transparent gif</a></li>
        </ul>
        <footer>
            <small>Photo by <a href="https://pixabay.com/photo-1150319/" target="photostock">YankoPeyankov</a> at pixabay.com</small>
        </footer>
    </body>
</html>