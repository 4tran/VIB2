<?php
require '../../../res/config.php';
require $config['root'] . '/res/twig_loader.php';

// Load board config and set variables to be used more than once.
$thread = json_decode(file_get_contents("index.json"), true);
$uri = $thread['uri'];
$id = $thread['id'];
$title = $thread['title'];

// Get thread posts.
$query = "select * from posts where uri = :uri and op = :op order by id asc";
$query = $db->prepare($query);
$query->bindParam(':uri', $uri);
$query->bindParam(':op', $id);
$query->execute();
$posts = $query->fetchAll();

// After all the logic is done, render the index.
echo $twig->render('thread.html', array(
    'title' => $title,
    'posts' => $posts,
    'uri' => $uri,
    'type' => 'reply'
));
?>
