<?php
require '../../../res/config.php';
require $config['root'] . '/res/twig_loader.php';

// Check for mod login
session_start();
$permission = $_SESSION['permission'];

// Get list of boards
$query = $db->query("select uri from boards");
$boards = $query->fetchAll();

// Load board config and set variables to be used more than once.
$thread = json_decode(file_get_contents("index.json"), true);
$uri = $thread['uri'];
$op = $thread['op'];
$title = $thread['title'];

// Get thread posts.
$query = $db->prepare("select * from posts where uri = :uri and op = :op order by id asc");
$query->bindValue(':uri', $uri);
$query->bindValue(':op', $op);
$query->execute();
$posts = $query->fetchAll();

// Render the index.
echo $twig->render('thread.html', array(
    'permission' => $permission,
    'boards' => $boards,
    'title' => $title,
    'posts' => $posts,
    'uri' => $uri,
    'op' => $op,
    'type' => 'reply'
));
?>
