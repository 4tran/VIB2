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
        mkdir("$dir/$id", 0777);
        mkdir("$dir/$id/res", 0777);

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
        // Ensure the image is actually an image.
        if (getimagesize($image['tmp_name'])['mime'] == 'image/jpeg' or 'image/png' or 'image/gif') {
            $ext = image_type_to_extension(getimagesize($image['tmp_name'])[2]);
            $time = time();
            $img_dir = "$dir/$op/res/$time$ext";
            move_uploaded_file($image['tmp_name'], $img_dir);
            $image = "/$uri/$op/res/$time$ext";

            // Create thumbnail.
            switch($ext) {
            case 'gif' :
                $tmp_img = imagecreatefromgif($img_dir);
                break;
            case 'png' :
                $tmp_img = imagecreatefrompng($img_dir);
                break;
            case 'jpg' or 'jpeg' :
                $tmp_img = imagecreatefromjpeg($img_dir);
                break;
            default:
                break;
            }
            $width = 150;
            $height = 150;
            list($width_orig, $height_orig) = getimagesize($img_dir);
            $ratio_orig = $width_orig/$height_orig;
            if ($width/$height > $ratio_orig) {
                $width = $height*$ratio_orig;
            }
            else {
                $height = $width/$ratio_orig;
            }
            $thumbnail = imagecreatetruecolor($width, $height);
            imagecopyresampled($thumbnail, $tmp_img, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
            imagejpeg($thumbnail, "$dir/$op/res/thumb_$time$ext");
            imagedestroy($thumbnail);
            $thumbnail = "/$uri/$op/res/thumb_$time$ext"; 
        }
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
    //header("Location: /$uri/$op");
}
?>
