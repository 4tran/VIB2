<?php
require '../../../res/config.php';
require $config['root'] . '/res/twig_loader.php';

// Get list of boards
$query = $db->prepare("select uri from boards");
$query->execute();
$boards = $query->fetchAll();

// Load board config and set variables to be used more than once.
$thread = json_decode(file_get_contents("index.json"), true);
$uri = $thread['uri'];
$op = $thread['op'];
$title = $thread['title'];

// Get thread posts.
$query = "select * from posts where uri = :uri and op = :op order by id asc";
$query = $db->prepare($query);
$query->bindValue(':uri', $uri);
$query->bindValue(':op', $op);
$query->execute();
$posts = $query->fetchAll();

// After all the logic is done, render the index.
echo $twig->render('thread.html', array(
    'boards' => $boards,
    'title' => $title,
    'posts' => $posts,
    'uri' => $uri,
    'op' => $op,
    'type' => 'reply'
));
?>
