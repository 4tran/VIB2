<?php
require '../res/config.php';
require $config['root'] . '/res/twig_loader.php';

echo $twig->render('site_index.html', array('title' => $config['site_name']));
?>
