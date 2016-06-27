<?php
require '../res/config.php';
require $config['root'] . '/res/twig_loader.php';

// Define variables to be used more than once.
$uri = $_POST['uri'];
$op = $_POST['uri'];
$type = $_POST['type'];
$name = htmlspecialchars($_POST['name']);
$content = htmlspecialchars($_POST['content']);
$image = $_FILES['image'];
$errors = array();

// Things that are true of both new threads and replies.

if ($name = '') {
    $name = 'Anonymous';
}
if (strlen($content) < 5) {                                                                                                                     
    array_push($errors, 'Post content too short.');                                                                                             
}
if ($image > 5000000) {
    array_push($errors, 'Image too large.');
}

// Things only to be applied to threads.
if ($type == 'thread') {
    if ($image['name'] == '') {
        array_push($errors, 'New threads must have an image.');
    }
    $dir = $config['root'] . "/res/$uri";
}

// Things only to be applied to replies.
if ($type == 'reply') {
    $dir = $config['root'] . "res/$uri/$op";
}
?>
