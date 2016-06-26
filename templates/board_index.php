<?php
require '../../res/config.php';
require $config['root'] . '/res/twig_loader.php';

// Load board config and set variables to be used more than once.
$board_config = json_decode(file_get_contents("index.json"), true);
$uri = $board_config['uri'];
$title = $board_config['title'];
$subtitle = $board_config['subtitle'];

// Get a list of original posts.
$query = "select id from posts where uri = :uri and id = op order by bump desc";
$query = $db->prepare($query);
$query->bindParam(':uri', $uri);
$query->execute();
$op_ids = $query->fetchAll();

// Get last 3 replies from threads.
$query = "select * from posts where uri = :uri and id = :op  union select * from (select * from posts where uri = :uri and op = :op order by id desc limit 3) x     order by id asc";
$query = $db->prepare($query);
$query->bindParam(':uri', $uri);
for ($i = 0; $i < count($op_ids); $i++) {
    $query->bindParam(':op', $op_ids[$i]['id']);
    $query->execute();
    if ($i < 1) {
        $posts = preg_replace("/(}])/mi", "},", json_encode($query->fetchAll()));
    }
    else {
        $posts .= preg_replace("/(\\[{)/mi", "{", json_encode($query->fetchAll()));
    }   
}
$posts = json_decode($posts);
// After all the logic is done, render the index.
echo $twig->render('board_index.html', array(
    'title' => $title,
    'subtitle' => $subtitle,
    'posts' => $posts
));
?>
