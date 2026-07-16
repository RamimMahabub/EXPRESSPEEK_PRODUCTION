<?php
foreach (glob('storage/framework/views/*.php') as $f) {
    if (strpos(file_get_contents($f), 'help.blade.php') !== false) {
        echo "FOUND: $f\n";
        echo file_get_contents($f);
    }
}
