<?php
require '../../res/config.php';
require $config['root'] . '/res/twig_loader.php';

// Get list of boards
$query = $db->prepare("select uri from boards");
$query->execute();
$boards = $query->fetchAll();

// Load board config and set variables to be used more than once.
$board_config = json_decode(file_get_contents("index.json"), true);
$uri = $board_config['uri'];
$title = $board_config['title'];
$subtitle = $board_config['subtitle'];

// Get a list of original posts.
$query = "select id from posts where uri = :uri and id = op order by bump desc";
$query = $db->prepare($query);
$query->bindValue(':uri', $uri);
$query->execute();
$op_ids = $query->fetchAll();

// Ensure there are posts, otherwise errors will be thrown.
if (count($op_ids) == 0) {
    $posts[0]['content'] = 'No posts yet';
}
// If there are posts, go ahead and display them.
else {
    // Get last 3 replies from thread.
    for ($i = 0; $i < count($op_ids); $i++) {
        $id = $op_ids[$i][0];
        if ($i == 0) {
            $query = "select * from (select * from posts where uri = '" . $uri . "' and id = '" . $id . "' union select * from (select * from posts where uri = '" . $uri . "' and op = '" . $id . "' order by id desc limit 3) x order by id asc) x";
        }
        else {
            $query .= " union select * from (select * from posts where uri = '" . $uri . "' and id = '" . $id . "' union select * from (select * from posts where uri = '" . $uri . "' and op = '" . $id . "' order by id desc limit 3) x order by id asc) x";
        }
}
$query = $db->prepare($query);
$query->execute();
$posts = $query->fetchAll();
}

// After all the logic is done, render the index.
echo $twig->render('board_index.html', array(
    'boards' => $boards,
    'title' => $title,
    'subtitle' => $subtitle,
    'posts' => $posts,
    'uri' => $uri,
    'type' => 'thread'
));
?>
