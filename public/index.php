<?php
require '../res/config.php';
require '../res/twig_loader.php';

echo $twig->render('index.html', array('title' => $config['site_name']));
?>
