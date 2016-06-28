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
$ip = $_SERVER['REMOTE_ADDR'];
$dir = $config['root'] . "/public/$uri";
$errors = array();

// Things that are true of both new threads and replies.
// Set default name if user submitted name is blank.
if ($name = '') {
    $name = 'Anonymous';
}
// Check, before modifying content, if post is too short.
if (strlen($content) < 5) {                                                                                                                     
    array_push($errors, 'Post content too short.');                                                                                             
}
// Check if post is too long.
if (strlen($content) > 2000) {
    array_push($errors, 'Post content too long.');
}
if ($image > 5000000) {
    array_push($errors, 'Image too large.');
}

// Errors only to be applied to threads.
if ($type == 'thread') {
    if ($image['name'] == '') {
        array_push($errors, 'New threads must have an image.');
    }
}

// Format post.

// Section where post stuff is actually done, if there are no errors. 
if (count($errors) == 0) {
    // Get id of last post + 1.
    $query = $db->prepare("select id from posts where uri = :uri order by id desc limit 1");
    $query->bindParam(':uri', $uri);
    $query->execute();
    $id = $query->fetchAll()[0][0] + 1;

    // If the post is a new thread, some extra stuff's gonna have to be done.
    if ($type == 'thread') {
		// New posts will always have equal id and op fields.
        $op = $id;
        // Make directories for index and images.
        mkdir("$dir/$id");
        mkdir("$dir/$id/res");
        copy($config['root'] . "/templates/thread_index.php", $dir/$id);
    }
	// Verify and process image and make thumbnail.
    if ($image['name'] != '') {
        
    }

	// Insert data into database.
    $query = $db->prepare("insert into posts (uri, id, op, name, content, image, thumbnail, timestamp, bump, ip)
        values (:uri, :id, :op, :name, :content, :image, :thumbnail, :timestamp, :bump, :ip)");
    $query->bindParam(':uri', $uri);
    $query->bindParam(':id', $id);
	$query->bindParam(':op', $op);
	$query->bindParam(':name', $name);
	$query->bindParam(':content', $content);
	$query->bindParam(':image', $image);
	$query->bindParam(':thumbnail', $thumbnail);
	$query->bindParam(':timestamp', now());
	$query->bindParam(':bump', now());
    $query->bindParam(':ip', $ip);
    $query->execute();
}
?>
