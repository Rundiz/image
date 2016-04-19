<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Test Image manipulation class.</title>
    </head>
    <body>
        <h1>Rundiz Image manipulation class</h1>
        <h2>Instruction before test</h2>
        <ul>
            <li>Please verify that your php.ini display the errors and report all error level including strict.</li>
            <li>Please make sure that <strong><?php echo realpath('../processed-images'); ?></strong> folder is already exists and has write permission.</li>
            <li>
                Download photo from <a href="https://pixabay.com/photo-1150319/" target="photostock">this link</a> at least size L and save it to <?php echo realpath('../source-images'); ?> folder with these name and extensions.<br>
                (You must convert from JPG to GIF and PNG from photo editor program. Not just rename the file extension.)
                <ul>
                    <li>city-amsterdam.gif</li>
                    <li>city-amsterdam.jpg</li>
                    <li>city-amsterdam.png</li>
                    <li>city-amsterdam-jpg.png (This file should be jpg but rename file extension to png.)</li>
                    <li>city-amsterdam-text.jpg (This is text file with jpg extension.)</li>
                    <li>city-amsterdam-animated.gif (This is animation gif. You should create animation in this image. It is for test with Imagick functions only.)</li>
                </ul>
            </li>
        </ul>
        <p><a href="clear-all-processed-images.php">Clear all processed images</a></p>
        <h2>Tests</h2>
        <h3>Native PHP GD functions test</h3>
        <ul>
            <li><a href="native-gd-jpg-resize.php">resize jpg image</a></li>
            <li><a href="native-gd-png-resize.php">resize png image</a></li>
            <li><a href="native-gd-gif-resize.php">resize gif image</a></li>
            <li><a href="native-gd-gif-watermark-png.php">gif image with watermark png</a></li>
        </ul>
        <h3>Native PHP Imagick functions test</h3>
        <ul>
            <li><a href="native-imagick-jpg-resize.php">resize jpg image</a></li>
            <li><a href="native-imagick-png-resize.php">resize png image</a></li>
            <li><a href="native-imagick-gif-resize.php">resize gif image</a></li>
            <li><a href="native-imagick-gif-watermark-png.php">gif image with watermark png</a></li>
            <li><a href="native-imagick-animate-gif.php">resize, crop, rotate animated gif</a> (warning, this is slower than process non animated gif.)</li>
        </ul>
        <h3>Rundiz GD Image class test</h3>
        <ul>
            <li><a href="gd-basic-tests.php">GD basic tests</a></li>
            <li><a href="gd-resize-ratio.php">GD test resize by aspect ratio</a></li>
            <li><a href="gd-rotate.php">GD test rotate</a></li>
            <li><a href="gd-flip.php">GD test flip</a></li>
            <li><a href="gd-multi-process.php">GD test multiple process</a></li>
            <li><a href="gd-watermark-image.php">GD test watermark image</a></li>
            <li><a href="gd-watermark-text.php">GD test watermark text</a></li>
        </ul>
        <h3>Rundiz Imagick Image class test</h3>
        <ul>
            <li><a href="imagick-basic-tests.php">Imagick basic tests</a></li>
            <li><a href="imagick-resize-ratio.php">Imagick test resize by aspect ratio</a></li>
            <li><a href="imagick-rotate.php">Imagick test rotate</a></li>
            <li><a href="imagick-flip.php">Imagick test flip</a></li>
            <li><a href="imagick-multi-process.php">Imagick test multiple process</a></li>
            <li><a href="imagick-watermark-image.php">Imagick test watermark image</a></li>
            <li><a href="imagick-watermark-text.php">Imagick test watermark text</a></li>
        </ul>
        <footer>
            <small>Photo by <a href="https://pixabay.com/photo-1150319/" target="photostock">YankoPeyankov</a> at pixabay.com</small>
        </footer>
    </body>
</html>