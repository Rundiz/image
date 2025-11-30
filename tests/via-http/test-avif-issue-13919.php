<?php
require_once 'includes/include-rundiz-image.php';

$sourceImageDir = '../source-images/avif';
if (!is_dir($sourceImageDir)) {
    mkdir($sourceImageDir, 0777, true);
}

$testFilesURLs = [
    'https://github.com/AOMediaCodec/av1-avif/blob/master/testFiles/Apple/multilayer_examples/animals_00_multilayer_a1lx.avif',
    'https://github.com/AOMediaCodec/av1-avif/blob/master/testFiles/Apple/multilayer_examples/animals_00_multilayer_grid_a1lx.avif',
    'https://github.com/AOMediaCodec/av1-avif/blob/master/testFiles/Apple/multilayer_examples/animals_00_singlelayer.avif',
];

$ch = curl_init();
foreach ($testFilesURLs as $eachURL) {
    $fileName = basename($eachURL);
    if (!is_file($sourceImageDir . '/' . $fileName) && stripos($eachURL, 'github.com/') !== false) {
        // if there is no file and URL is GitHub.
        $URLPath = trim(parse_url($eachURL, PHP_URL_PATH), " \n\r\t\v\0/");
        $URLPathArray = explode('/', $URLPath);
        $githubURL = 'https://api.github.com/repos/' . $URLPathArray[0] . '/' . $URLPathArray[1] . '/contents/';
        unset($URLPathArray[0], $URLPathArray[1], $URLPathArray[2], $URLPathArray[3]);

        curl_setopt($ch, CURLOPT_URL, $githubURL . implode('/', $URLPathArray));
        curl_setopt($ch, CURLOPT_USERAGENT, 'GitHub.com/Rundiz/image');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/vnd.github.v3.raw',
        ]);
        $fp = fopen($sourceImageDir . '/' . $fileName, 'w+');
        if ($fp === false) {
            trigger_error('Failed to open local file for writing.', E_USER_WARNING);
        }
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_exec($ch);
        if (curl_errno($ch)) {
            trigger_error('cURL error: ' . curl_error($ch), E_USER_WARNING);
        }
        fclose($fp);
        
        unset($fp, $URLPath, $URLPathArray);
    } elseif (!is_file($sourceImageDir . '/' . $fileName)) {
        trigger_error('Please download file from ' . $eachURL . ' and save as &quot;' . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $sourceImageDir . '/' . $fileName) . '&quot;.', E_USER_WARNING);
    }
    unset($fileName);
}// endforeach;
unset($eachURL);
unset($ch);

unset($testFilesURLs);

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Test Image manipulation class.</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <h1>Test AVIF image supported</h1>
        <p>
            Based on <a href="https://github.com/php/php-src/issues/13919" target="github-php">issue 13919</a>. 
            Please set PHP to display all errors before to see all notice, warning, error.
        </p>
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th><code>imagecreatefromavif()</code> result</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $RDI = new \RecursiveDirectoryIterator($sourceImageDir, FilesystemIterator::SKIP_DOTS);
                $RII = new \RecursiveIteratorIterator($RDI);
                $RII->rewind();
                foreach ($RII as $File) {
                    if (strtolower($File->getExtension()) !== 'avif') {
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

                    echo '<td>' . "\n";
                    $img = imagecreatefromavif($File->getRealPath());
                    echo '<pre>' . var_export($img, true) . '</pre>' . "\n";
                    echo '</td>' . "\n";
                    echo '</tr>';

                    if (version_compare(PHP_VERSION, '8.0', '<')) {
                        imagedestroy($img);
                    }
                    unset($img);
                }// endforeach;
                unset($File);
                unset($RDI, $RII);
                ?> 
            </tbody>
        </table>
        <hr>
        <?php 
        unset($sourceImageFile);
        include 'includes/include-page-footer.php';
        ?>
    </body>
</html>