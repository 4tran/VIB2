<?php
function rmrf($path) {
    if (! is_dir($path)) {
        echo "Invalid directory.";
    }
    if (substr($path, strlen($path) - 1, 1) != '/') {
        $path .= '/';
    }
    $files = glob($path . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            rmrf($file);
        }
        else {
            unlink($file);
        }
    }
    rmdir($path);
}
rmrf("/var/www/public/test1");
?>
