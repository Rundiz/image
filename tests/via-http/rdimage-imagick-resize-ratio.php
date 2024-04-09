<?php
require_once 'includes/include-rundiz-image.php';

require __DIR__.DIRECTORY_SEPARATOR.'include-image-source.php';
include_once 'includes/include-functions.php';


$imgType = (isset($_GET['imgType']) ? $_GET['imgType'] : 'JPG');
$imgType = strip_tags($imgType);


function displayTestResizeRatio(array $test_data_set)
{
    $resizeDims = [
        [300, 300],
        [400, 700],
        [2100, 1500],
    ];
    $settings = [
        [
            'allow_resize_larger' => [false, 'Not allowed resize larger'],
            'master_dim' => ['auto', 'Master dimension auto'], 
        ],
        [
            'allow_resize_larger' => [true, 'Allowed resize larger'],
            'master_dim' => ['auto', 'Master dimension auto'], 
        ],
        [
            'allow_resize_larger' => [false, 'Not allowed resize larger'],
            'master_dim' => ['height', 'Master dimension height'], 
        ],
    ];
    echo '<h1>Resize by aspect ratio (Imagick)</h1>' . "\n";
    foreach ($test_data_set as $img_type_name => $item) {
        echo '<h3>' . $img_type_name . '</h3>' . "\n";
        if (is_array($item) && array_key_exists('source_image_path', $item)) {
            echo '<table><tbody>' . "\n";
            echo '<tr>' . "\n";
            echo '<td style="width: 200px;">Source image</td>' . "\n";
            echo '<td>' . "\n";
            debugImage($item['source_image_path']);
            echo '</td>' . "\n";
            echo '</tr>' . "\n";

            foreach ($settings as $eachSettingsSet) {
                echo '<tr>' . "\n";
                echo '<td>';
                $settingsMsg = [];
                $exampleCodeMsg = [];
                foreach ($eachSettingsSet as $settingProp => $settingValueDesc) {
                    $settingsMsg[] = $settingValueDesc[1];
                    $exampleCodeMsg[] = '$Image->' . $settingProp . ' = ' . var_export($settingValueDesc[0], true) . ';';
                }// endforeach each settings set.
                unset($settingProp, $settingValueDesc);
                echo implode(', ', $settingsMsg) . "\n";
                echo '<pre>' . implode("\n", $exampleCodeMsg) . '</pre>' . "\n";
                unset($exampleCodeMsg, $settingsMsg);
                echo '</td>' . "\n";

                foreach ($resizeDims as $eachDim) {
                    $file_name = '../processed-images/' . autoImageFilename() . 
                        '_src' . str_replace(' ', '-', strtolower($img_type_name)) .
                        '_' . $eachDim[0] . 'x' . $eachDim[1];
                    $Image = new \Rundiz\Image\Drivers\Imagick($item['source_image_path']);
                    foreach ($eachSettingsSet as $settingProp => $settingValueDesc) {
                        $Image->{$settingProp} = $settingValueDesc[0];
                        $file_name .= '_' . str_replace([' ', '\'', '_'], '', $settingProp) . '-' .
                            str_replace([' ', '\'', '"', '_'], '', trim(var_export($settingValueDesc[0], true)));
                    }// endforeach each settings set.
                    $file_name .= '.' . pathinfo($item['source_image_path'], PATHINFO_EXTENSION);
                    unset($settingProp, $settingValueDesc);
                    $Image->resize($eachDim[0], $eachDim[1]);
                    $saveResult = $Image->save($file_name);
                    echo '<td>' . "\n";
                    if ($saveResult === true) {
                        echo '<a href="' . $file_name . '"><img src="' . $file_name . '" alt="" class="thumbnail"></a><br>';
                        $img_data = getimagesize($file_name);
                        echo  '<strong>' . $eachDim[0] . 'x' . $eachDim[1];
                        if (is_array($img_data) && isset($img_data[0]) && isset($img_data[1])) {
                            echo ' =&gt; ';
                            echo $img_data[0] . 'x' . $img_data[1] . ' ';
                        }
                        echo '</strong>' . "\n";
                        debugImage($file_name, ['dataOnly' => true]);
                    } else {
                        echo ' &nbsp; &nbsp; <span class="text-error">Error: ' . $Image->status_msg . '</span>' . "\n";
                    }
                    echo '</td>' . "\n";
                    $Image->clear();
                    unset($Image);
                    unset($file_name, $saveResult);
                }// endforeach; dimensions
                unset($eachDim);

                echo '</tr>' . "\n";
            }// endforeach; settings
            unset($eachSettingsSet);

            echo '</tbody></table>' . "\n";
            echo "\n\n";
        }// endforeach;
    }
    unset($resizeDims, $settings);
}// displayTestResizeRatio


function displayTestSaveCrossExts(array $test_data_set)
{
    global $saveAsExts;

    echo '<h2>Resize &amp; Save across different extensions.</h2>' . "\n";
    foreach ($test_data_set as $img_type_name => $item) {
        echo '<h3>'.$img_type_name.'</h3>'."\n";
        if (is_array($item) && array_key_exists('source_image_path', $item)) {
            echo '<table><tbody>' . "\n";
            echo '    <tr>' . "\n";
            echo '        <td style="width: 200px;">Source image</td>' . "\n";
            echo '        <td>' . "\n";
            $srcImageSize = false;
            if (is_file($item['source_image_path'])) {
                $srcImageSize = getimagesize($item['source_image_path']);
            }
            debugImage($item['source_image_path']);
            echo '        </td>'."\n";
            $Image = new Rundiz\Image\Drivers\Imagick($item['source_image_path']);
            echo '    </tr>' . "\n";

            echo '    <tr>' . "\n";
            echo '        <td>Save as</td>' . "\n";
            foreach ($saveAsExts as $eachExt) {
                $Image->resize(800, 600);
                $file_name = '../processed-images/' . autoImageFilename() . '-src-' . str_replace(' ', '-', strtolower($img_type_name)) . '-800x600' .
                    '-saveas-' . trim($eachExt) . '.' . $eachExt;
                $saveResult = $Image->save($file_name);
                $statusMsg = $Image->status_msg;
                $Image->clear();
                echo '        <td>' . "\n";
                debugImage($file_name);
                if ($saveResult != true) {
                    echo '            &nbsp; &nbsp; <span class="text-error">Error: '.$statusMsg.'</span>'."\n";
                }
                echo '        </td>' . "\n";
                unset($file_name, $saveResult, $statusMsg);
            }// endforeach; save extensions
            unset($eachExt);
            echo '    </tr>' . "\n";
            unset($Image);

            echo '    <tr>' . "\n";
            echo '        <td>Use <code>show()</code> method as</td>' . "\n";
            foreach ($saveAsExts as $eachExt) {
                $linkTo = 'rdimage-imagick-show-image.php?source_image_file=' . rawurldecode($item['source_image_path']) . 
                    '&amp;show_ext=' . $eachExt .
                    '&amp;act=resize' .
                    '&amp;width=' . 800 .
                    '&amp;height=' . 600;
                echo '        <td>';
                echo '<a href="' . $linkTo . '"><img class="thumbnail" src="' . $linkTo . '" alt=""></a><br>';
                echo 'Extension: ' . $eachExt;
                unset($linkTo);
                echo '</td>' . "\n";
            }// endforeach; save extensions
            unset($eachExt);
            echo '    </tr>' . "\n";

            echo '</tbody></table>' . "\n";
            
            unset($srcImageSize);
        }// endif;
        echo "\n\n";
    }// endforeach;
    unset($img_type_name, $item);

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
        <?php
        // default do test data set.
        $doTestData = [
            $imgType => [],
        ];
        // set do test data from parameter.
        if (array_key_exists($imgType, $test_data_set)) {
            $doTestData = [$imgType => $test_data_set[$imgType]];
        } else {
            if (array_key_exists($imgType, $test_data_falsy)) {
                $doTestData = [$imgType => $test_data_falsy[$imgType]];
            } elseif (array_key_exists($imgType, $test_data_anim)) {
                $doTestData = [$imgType => $test_data_anim[$imgType]];
            }
        }
        displayTestResizeRatio($doTestData);
        displayTestSaveCrossExts($doTestData);
        unset($doTestData);
        ?>
        <hr>
        <?php
        include 'includes/include-page-footer.php';
        ?>
    </body>
</html>