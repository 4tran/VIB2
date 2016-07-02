<?php
require '../res/config.php';
require $config['root'] . '/res/twig_loader.php';
require $config['root'] . '/inc/bulletproof/src/bulletproof.php';
require $config['root'] . '/inc/bulletproof/src/utils/func.image-resize.php';

// Define variables to be used more than once.
$uri = $_POST['uri'];
$op = $_POST['op'];
$type = $_POST['type'];
$name = htmlspecialchars($_POST['name']);
$content = htmlspecialchars($_POST['content']);
$image = $_FILES['image'];
$ip = $_SERVER['REMOTE_ADDR'];
$dir = $config['root'] . "/public/$uri";
$errors = array();

// Things that are true of both new threads and replies.
// Set default name if user submitted name is blank.
if (empty($name)) {
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
// If the image doesn't exist, set variables accordingly (for query)
if (empty($image['name'])) {
    $image = '';
    $thumbnail = '';
}
// Errors only to be applied to threads.
if ($type == 'thread') {
    if (empty($image)) {
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
    // Update the post count.
    $query = $db->prepare("update boards set post_count = post_count + 1 where uri = :uri");
    $query->bindValue(':uri', $uri);
    $query->execute();

    // Get the id of the new post.
    $query = $db->prepare("select post_count from boards where uri = :uri");
    $query->bindValue(':uri', $uri);
    $query->execute();
    $id = $query->fetchAll()[0][0];

    // If the post is a new thread, some extra stuff's gonna have to be done.
    if ($type == 'thread') {
		// New threads will always have equal id and op fields.
        $op = $id;
        // Make directory.
        mkdir("$dir/$id");

        // Render and create thread.json
        $thread_json = $twig->render('thread.json', array('uri' => $uri, 'op' => $op, 'content' => $content));
        $file_thread_json = fopen("$dir/$op/index.json", "w");
        fwrite($file_thread_json, $thread_json);
        fclose($file_thread_json);

        // Copy index file.
        copy($config['root'] . "/templates/thread.php", "$dir/$id/index.php");
    }

    // Verify and process image.
    if (!empty($image)) {
        $image = new Bulletproof\Image($_FILES);
        $image->setLocation("$dir/$op/res");
        $image->setSize(0, 5000000);
        $image->setDimension(5000, 5000);
        $image->setMime(array('jpeg', 'jpg', 'gif', 'png'));
        $image->upload();
        $image = "/$uri/$id/res/" . $image->getName() . "." . $image->getMime();
    }

    // Insert data into database.
    $query = $db->prepare("insert into posts (uri, id, op, name, content, image, thumbnail, ip)
        values (:uri, :id, :op, :name, :content, :image, :thumbnail, :ip)");
    $query->bindValue(':uri', $uri);
    $query->bindValue(':id', $id);
	$query->bindValue(':op', $op);
	$query->bindValue(':name', $name);
	$query->bindValue(':content', $content);
    $query->bindValue(':image', $image);
    $query->bindValue(':thumbnail', $thumbnail);
    $query->bindValue(':ip', $ip);
    $query->execute();

    // If the post is a reply, the thread needs to be bumped.
    if ($type == 'reply') {
        $query = $db->prepare("update posts set bump = now() where uri = :uri and id = :id");
        $query->bindValue(':uri', $uri);
        $query->bindValue(':id', $op);
        $query->execute();
    }

    // If all goes well, the user will be redirected to either their new thread, or the thread they had posted in.
    header("Location: /$uri/$op");
}
?>
