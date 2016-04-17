<?php
echo '<footer>';
echo 'Memory<br>'."\n";
echo 'Current: '.round(memory_get_usage()/1048576,2).' MB';
echo ', ';
echo 'Max: '.round(memory_get_peak_usage()/1048576,2).' MB';
echo '</footer>'."\n";