<?php
require '../../res/config.php';
require $config['root'] . '/res/twig_loader.php';

// Load board config and set variables to be used more than once.
$board_config = json_decode(file_get_contents("index.json"), true);
$uri = $board_config['uri'];
$title = $board_config['title'];
$subtitle = $board_config['subtitle'];

// Get post data from database.
$query = "select * from posts where uri = :uri";
$query = $db->prepare($query);
$query->bindParam(':uri', $uri);
$query->execute();
$posts = $query->fetchAll();

// After all the logic is done, render the index.
echo $twig->render('board_index.html', array(
    'title' => $title,
    'subtitle' => $subtitle,
    'posts' => $posts
));
?>
