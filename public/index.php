<?php
require '../res/config.php';
require $config['root'] . '/res/twig_loader.php';

// Get list of boards
$query = $db->prepare("select title, uri from boards");
$query->execute();
$boards = $query->fetchAll();

// Get list of latest posts.
$query = $db->prepare("select uri, content, id, op from posts order by timestamp desc limit 15");
$query->execute();
$posts = $query->fetchAll();
for ($i = 0; $i < count($posts); $i++) {
    $posts[$i]['content'] = substr(strip_tags(trim(preg_replace("/\s+/", " ", $posts[$i]['content']))), 0, 150);
}

// Get list of latest images.
$query = $db->prepare("select * from (select thumbnail, op, id, uri from posts where thumbnail <> '' order by timestamp desc) x limit 5");
$query->execute();
$images = $query->fetchAll();

// Get total amount of active posts
$query = $db->prepare("select count(id) from posts");
$query->execute();
$active_posts = $query->fetchAll()[0][0];

// Get total amount of all posts since the beginning of time
$query = $db->prepare("select sum(post_count) from boards");
$query->execute();
$total_posts = $query->fetchAll()[0][0];

echo $twig->render('site_index.html', array(
    'title' => $config['site_name'],
    'boards' => $boards,
    'posts' => $posts,
    'images' => $images,
    'active_posts' => $active_posts,
    'total_posts' => $total_posts
));
?>
