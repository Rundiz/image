<?php
echo '<footer>';
echo 'PHP: ' . PHP_VERSION . '<br>' . "\n";
echo 'Memory ';
echo 'Current: '.round(memory_get_usage()/1048576, 2).' MB';
echo ', ';
echo 'Max: '.round(memory_get_peak_usage()/1048576, 2).' MB';
if (isset($_SERVER['REQUEST_TIME_FLOAT'])) {
    echo '<br>' . "\n";
    echo 'Page load time: ';
    echo (microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) . ' seconds';
}
echo '</footer>'."\n";