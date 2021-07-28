<?php
require_once 'include-rundiz-image.php';

require __DIR__.DIRECTORY_SEPARATOR.'include-image-source.php';


$imgType = (isset($_GET['imgType']) ? $_GET['imgType'] : 'JPG');
$imgType = strip_tags($imgType);


function displayTestsConstructor(array $test_data_set)
{
    echo '<h2>Constructor image data.</h2>'."\n";
    foreach ($test_data_set as $img_type_name => $item) {
        echo '<h3>'.$img_type_name.'</h3>'."\n";
        if (is_array($item) && array_key_exists('source_image_path', $item)) {
            echo 'Source image: ';
            if (is_file($item['source_image_path'])) {
                echo '<a href="'.$item['source_image_path'].'"><img src="'.$item['source_image_path'].'" alt="" class="thumbnail"></a>'."\n";
            }
            $Image = new \Rundiz\Image\Drivers\Gd($item['source_image_path']);
            echo '<pre class="mini-data-box">'.print_r($Image, true).'</pre>'."\n";
            if ($Image->status !== true) {
                echo '<p class="text-error">' . $Image->status_msg . '</p>' . "\n";
                $sourceExt = strtolower(pathinfo($item['source_image_path'], PATHINFO_EXTENSION));
                if ($sourceExt === 'webp') {
                    $WebP = new \Rundiz\Image\Extensions\WebP();
                    echo 'WebP info' . "\n";
                    echo '<pre class="mini-data-box">';
                    echo var_export($WebP->webPInfo($item['source_image_path']), true);
                    echo '</pre>' . "\n";
                    unset($WebP);
                }// endif; soure ext
                unset($sourceExt);
            }// endif status
            unset($Image);
        }
        echo "\n\n";
    }// endforeach;
    echo "\n\n";
}// displayTestsConstructor


function displayTestSaveCrossExts(array $test_data_set)
{
    $saveExts = ['gif', 'jpg', 'png', 'webp'];
    echo '<h2>Save across different extensions.</h2>' . "\n";
    foreach ($test_data_set as $img_type_name => $item) {
        echo '<h3>'.$img_type_name.'</h3>'."\n";
        if (is_array($item) && array_key_exists('source_image_path', $item)) {
            echo '<table><tbody>' . "\n";
            echo '<tr>' . "\n";
            echo '<td style="width: 200px;">Source image</td><td><a href="'.$item['source_image_path'].'"><img src="'.$item['source_image_path'].'" alt="" class="thumbnail"></a><br>';
            $srcImageSize = false;
            if (is_file($item['source_image_path'])) {
                $srcImageSize = getimagesize($item['source_image_path']);
                if (is_array($srcImageSize)) {
                    echo $srcImageSize[0] . 'x' . $srcImageSize[1] . ' ';
                    echo 'Mime type: ' . $srcImageSize['mime'];
                }
            }
            echo '</td>'."\n";
            $Image = new Rundiz\Image\Drivers\Gd($item['source_image_path']);
            echo '</tr>' . "\n";

            echo '<tr>' . "\n";
            echo '<td>Save as</td>' . "\n";
            foreach ($saveExts as $eachExt) {
                $file_name = '../processed-images/' . basename(__FILE__, '.php') . '_src'.str_replace(' ', '-', strtolower($img_type_name)) .
                    '_target' . trim($eachExt) . '.' . $eachExt;
                $saveResult = $Image->save($file_name);
                $statusMsg = $Image->status_msg;
                $Image->clear();
                echo '<td>';
                echo '<a href="' . $file_name . '"><img class="thumbnail" src="' . $file_name . '" alt=""></a><br>';
                echo 'Extension: ' . $eachExt;
                if ($saveResult != true) {
                    echo ' &nbsp; &nbsp; <span class="text-error">Error: '.$statusMsg.'</span>'."\n";
                } else {
                    $Finfo = new finfo();
                    echo '; Mime type: ' . $Finfo->file($file_name, FILEINFO_MIME_TYPE);
                    unset($Finfo);
                }
                echo '</td>' . "\n";
                unset($file_name, $saveResult, $statusMsg);
            }// endforeach; save extensions
            unset($eachExt);
            echo '</tr>' . "\n";
            unset($Image);

            echo '<tr>' . "\n";
            echo '<td>Use <code>show()</code> method as</td>' . "\n";
            foreach ($saveExts as $eachExt) {
                $linkTo = 'rdimage-gd-show-image.php?source_image_file=' . rawurldecode($item['source_image_path']) . 
                    '&amp;show_ext=' . $eachExt .
                    '&amp;act=resize' .
                    '&amp;width=' . (is_array($srcImageSize) ? $srcImageSize[0] : 1920) .
                    '&amp;height=' . (is_array($srcImageSize) ? $srcImageSize[1] : 1080);
                echo '<td>';
                echo '<a href="' . $linkTo . '"><img class="thumbnail" src="' . $linkTo . '" alt=""></a><br>';
                echo 'Extension: ' . $eachExt;
                unset($linkTo);
                echo '</td>' . "\n";
            }// endforeach; save extensions
            unset($eachExt);
            echo '</tr>' . "\n";

            echo '</tbody></table>' . "\n";
            
            unset($srcImageSize);
        }// endif;
        echo "\n\n";
    }// endforeach;
    unset($img_type_name, $item);

    unset($saveExts);
    echo "\n\n";
}// displayTestSaveCrossExts
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Test Image manipulation class.</title>
        <link rel="stylesheet" href="./style.css">
    </head>
    <body>
        <h1>GD basic tests</h1>
        <?php 
        // default do test data set.
        $doTestData = [
            $imgType => [],
        ];
        // set do test data from parameter.
        if (array_key_exists($imgType, $test_data_set)) {
            $doTestData = [$imgType => $test_data_set[$imgType]];
        } else {
            if (array_key_exists($imgType, $test_data_pngnt)) {
                $doTestData = [$imgType => $test_data_pngnt[$imgType]];
            } elseif (array_key_exists($imgType, $test_data_falsy)) {
                $doTestData = [$imgType => $test_data_falsy[$imgType]];
            }
        }
        displayTestsConstructor($doTestData);
        ?><hr>
        <?php
        displayTestSaveCrossExts($doTestData);
        unset($doTestData);
        ?>
        <hr>
        <?php
        include __DIR__.DIRECTORY_SEPARATOR.'include-memory-usage.php';
        ?>
    </body>
</html>