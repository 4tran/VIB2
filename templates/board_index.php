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
$page = $_POST['page'];

// Function to get list of ops for selected page.
function loadPage($page) {
	global $op_ids, $db, $uri;
    $limit = 10*$page;
    $query = $db->prepare("select id from (select * from (select * from posts where uri = :uri and id = op order by bump desc limit :limit) x order by bump asc limit 10) x order by bump desc");
    $query->bindValue(':uri', $uri);
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->execute();
    $op_ids = $query->fetchAll();
}
// If a page isn't selected, assume page 1.
if (empty($page)) {
	$page = 1;
}

// Load the page's ops.
loadPage($page);

// Ensure there are posts, otherwise errors will be thrown.
if (count($op_ids) == 0) {
    $posts[0]['content'] = 'No posts yet';
}
// If there are posts, go ahead and display them.
if (count($op_ids) > 0) {
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
