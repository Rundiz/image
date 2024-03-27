<?php
require_once 'include-rundiz-image.php';

$sourceImageDir = '../source-images';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Test Image manipulation class.</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <h1>Show image source data.</h1>
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th><code>getImageFileData()</code></th>
                    <th>Specific extension data</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $supportedExts = ['gif', 'jpeg', 'jpg', 'png', 'webp'];
                $RDI = new \RecursiveDirectoryIterator($sourceImageDir, FilesystemIterator::SKIP_DOTS);
                $RII = new \RecursiveIteratorIterator($RDI);
                $RII->rewind();
                foreach ($RII as $File) {
                    if (!in_array(strtolower($File->getExtension()), $supportedExts)) {
                        continue;
                    }

                    echo '<tr>' . "\n";
                    echo '<td>' . "\n";
                    $imageUrl = str_replace(['\\', DIRECTORY_SEPARATOR], '/', $File->getPathname());
                    echo '<a href="' . $imageUrl . '"  title="' . htmlspecialchars($File->getRealPath()) . '">';
                    echo '<img class="img-fluid thumbnail" src="' . $imageUrl . '" alt="">';
                    echo '</a><br>';
                    echo '<span title="' . htmlspecialchars($File->getRealPath()) . '">' . $File->getFilename() . '</span><br>';
                    echo '</td>' . "\n";

                    $Image = new \Rundiz\Image\Drivers\Gd($File->getRealPath());
                    echo '<td>' . "\n";
                    try {
                        echo '<pre>' . var_export($Image->getImageFileData($File->getRealPath()), true) . '</pre>' . "\n";
                    } catch (\Exception $ex) {
                        echo '<p class="text-error">' . $ex->getMessage() . ' (code ' . $ex->getCode() . ')' . '</p>' . "\n";
                    }
                    echo '</td>' . "\n";

                    echo '<td>' . "\n";
                    if (strtolower($File->getExtension()) === 'gif') {
                        echo '<strong>GIF</strong><br>' . "\n";
                        $Gif = new Rundiz\Image\Extensions\Gif();
                        echo '<pre>' . var_export($Gif->gifInfo($File->getRealPath()), true) . '</pre>' . "\n";
                        unset($Gif);
                    } elseif (strtolower($File->getExtension()) === 'webp') {
                        echo '<strong>WEBP</strong><br>' . "\n";
                        $WebP = new Rundiz\Image\Extensions\WebP($File->getRealPath());
                        echo '<pre>' . var_export($WebP->webPInfo(), true) . '</pre>' . "\n";
                        unset($WebP);
                    }
                    echo '</td>' . "\n";
                    echo '</tr>' . "\n";
                    unset($Image, $imageUrl);
                }
                unset($RDI, $RII, $supportedExts);
                ?> 
            </tbody>
        </table>
        <hr>
        <?php 
        unset($sourceImageFile);
        include __DIR__.DIRECTORY_SEPARATOR.'include-memory-usage.php';
        ?>
    </body>
</html>