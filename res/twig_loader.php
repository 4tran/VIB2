<?php
require 'config.php';
require_once $config['root'] . '/inc/twig/lib/Twig/Autoloader.php';
Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem('../templates');
$twig = new Twig_Environment($loader);
?>
