<?php
require '../res/config.php';
require $config['root'] . '/res/twig_loader.php';
require $config['root'] . '/inc/bulletproof/src/bulletproof.php';
require $config['root'] . '/inc/bulletproof/src/utils/func.image-resize.php';

// Define variables to be used more than once.
$uri = $_POST['uri'];
$op = $_POST['uri'];
$type = $_POST['type'];
$name = htmlspecialchars($_POST['name']);
$content = htmlspecialchars($_POST['content']);
$image = new Bulletproof\Image($_FILES);
$ip = $_SERVER['REMOTE_ADDR'];
$dir = $config['root'] . "/public/$uri";
$errors = array();

// Things that are true of both new threads and replies.
// Set default name if user submitted name is blank.
if ($name == '') {
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

// Errors only to be applied to threads.
if ($type == 'thread') {
    if ($_FILES['image']['tmp_name'] == '') {
        array_push($errors, 'New threads must have an image.');
    }
}
// Format post.

// If there are errors, spit them out to the user.
if (count($errors) > 0) {
    echo $twig->render('post_errors.html', array('title' => $config['site_name'], 'errors' => $errors));
}

// If there are no errors, create the post. 
if (count($errors) == 0) {
    // Get id of last post + 1.
    $query = $db->prepare("select id from posts where uri = :uri order by id desc limit 1");
    $query->bindParam(':uri', $uri);
    $query->execute();
    $id = $query->fetchAll()[0][0] + 1;

    // If the post is a new thread, some extra stuff's gonna have to be done.
    if ($type == 'thread') {
		// New threads will always have equal id and op fields.
        $op = $id;
        // Make directory.
        mkdir("$dir/$id");

        // Render and create thread.json
        $thread_json = $twig->render('thread.json', array('uri' => $uri, 'id' => $id, 'content' => $content));
        $file_thread_json = fopen("$dir/$op/index.json", "w");
        fwrite($file_thread_json, $thread_json);
        fclose($file_thread_json);

        // Copy index file.
        copy($config['root'] . "/templates/thread.php", "$dir/$id/index.php");
    }

	// Verify and process image.
    if ($image['image']) {
        $image->setLocation("$dir/$op/res");
        $image->setSize(0, 5000000);
        $image->setDimension(5000, 5000); 
        $image->setMime(array('jpeg', 'jpg', 'gif', 'png'));
        $image->upload();
        $image = "/$uri/$id/res/" . $image->getName() . "." . $image->getMime();
    }

	// Insert data into database.
    $query = $db->prepare("insert into posts (uri, id, op, name, content, image, ip)
        values (:uri, :id, :op, :name, :content, :image, :ip)");
    $query->bindParam(':uri', $uri);
    $query->bindParam(':id', $id);
	$query->bindParam(':op', $op);
	$query->bindParam(':name', $name);
	$query->bindParam(':content', $content);
	$query->bindParam(':image', $image);
    $query->bindParam(':ip', $ip);
    $query->execute();

    // If the post is a reply, the thread needs to be bumped.
    if ($type == 'reply') {
        $query = $db->prepare("update posts set bump = :bump where uri = :uri and id = :id");
        $query->bindParam(':bump', now());
        $query->bindParam(':uri', $uri);
        $query->bindParam(':id', $op);
        $query->execute();
    }

    // If all goes well, the user will be redirected to either their new thread, or the thread they had posted in.
    header("Location: /$uri/$op");
}
?>
