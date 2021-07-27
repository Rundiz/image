<?php
require_once 'include-rundiz-image.php';

require __DIR__.DIRECTORY_SEPARATOR.'include-image-source.php';


function displayTestResizeRatio(array $test_data_set)
{
    $resizeDims = [
        [300, 300],
        [900, 400],
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
    echo '<h1>Resize by aspect ratio (Imagick)</h1>'."\n";
    foreach ($test_data_set as $img_type_name => $item) {
        echo '<h3>'.$img_type_name.'</h3>'."\n";
        if (is_array($item) && array_key_exists('source_image_path', $item)) {
            echo '<table><tbody>' . "\n";
            echo '<tr>' . "\n";
            echo '<td style="width: 200px;">Source image</td><td><a href="'.$item['source_image_path'].'"><img src="'.$item['source_image_path'].'" alt="" class="thumbnail"></a><br>';
            $imgData = getimagesize($item['source_image_path']);
            if (is_array($imgData)) {
                echo $imgData[0] . 'x' . $imgData[1] . ' ';
                echo 'Mime type: ' . $imgData['mime'];
            }
            unset($imgData);
            echo '</td>'."\n";
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
                    $file_name = '../processed-images/' . basename(__FILE__, '.php') . 
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
                    $Image->clear();
                    unset($Image);
                    echo '<td>';
                    if ($saveResult === true) {
                        echo '<a href="' . $file_name . '"><img src="' . $file_name . '" alt="" class="thumbnail"></a><br>';
                        $img_data = getimagesize($file_name);
                        echo  $eachDim[0] . 'x' . $eachDim[1];
                        if (is_array($img_data) && isset($img_data[0]) && isset($img_data[1])) {
                            echo ' =&gt; ';
                            echo $img_data[0] . 'x' . $img_data[1] . ' ';
                        }
                    } else {
                        echo ' &nbsp; &nbsp; <span class="text-error">Error: '.$Image->status_msg . '</span>' . "\n";
                    }
                    echo '</td>' . "\n";
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
        // add animated GIF after GIF.
        $test_data_set = array_slice($test_data_set, 0, 3, true) +
            ['GIF Animation' => [
                'source_image_path' => $source_image_animated_gif,
            ]] +
        array_slice($test_data_set, 3, NULL, true);
        // display test
        displayTestResizeRatio($test_data_set);
        unset($test_data_set);
        ?>
        <hr>
        <?php
        include __DIR__.DIRECTORY_SEPARATOR.'include-memory-usage.php';
        ?>
    </body>
</html>