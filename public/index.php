<?php
require '../res/config.php';
require $config['root'] . '/res/twig_loader.php';

// Get list of boards
$query = $db->prepare("select title, uri from boards");
$query->execute();
$boards = $query->fetchAll();

// Get list of latest posts.
$query = $db->prepare("select uri, content, id, op from posts order by id desc limit 10");
$query->execute();
$posts = $query->fetchAll();
for ($i = 0; $i < count($posts); $i++) {
    $posts[$i]['content'] = substr(trim(preg_replace("/\s+/", " ", $posts[$i]['content'])), 0, 80);
}

echo $twig->render('site_index.html', array(
    'title' => $config['site_name'],
    'boards' => $boards,
    'posts' => $posts
));
?>
