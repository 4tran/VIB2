<?php
require '../../res/config.php';
require $config['root'] . '/inc/rm-rf.php';

session_start();
$uri = $_POST['uri'];
$id = $_POST['id'];
$permission = $_SESSION['permission'];

// Check for valid login. And properly set parameters.
if (($permission == 'admin' or 'mod') && !empty($id) && !empty($uri)) {
    $query = $db->prepare("select * from posts where uri = :uri and id = :id");
    $query->bindValue(':uri', $uri);
    $query->bindValue(':id', $id);
    $query->execute();
    $post = $query->fetchAll()[0];

    // Individual post (reply)
    if ($post['op'] != $post['id']) {
        $image = $post['image'];
        $thumbnail = $post['thumbnail'];

        if (!empty($image)) {
            unlink($config['root'] . "/public/$image");
            unlink($config['root'] . "/public/$thumbnail");
        }

        $query = $db->prepare("delete from posts where uri = :uri and id = :id");
        $query->bindValue(':uri', $uri);
        $query->bindValue(':id', $id);
        $query->execute();
        header("Location: /$uri/" . $post['op']);
    }

    // OP (thread)
    else if ($post['op'] == $post['id']) {
        $op = $id;
        rmrf($config['root'] . "/public/$uri/$op");

        $query = $db->prepare("delete from posts where uri = :uri and op = :op");
        $query->bindValue(':uri', $uri);
        $query->bindValue(':op', $op);
        $query->execute();
        header("Location: /$uri");
    }
}
else {
    echo "You do not have permission to perform this function.";
}
?>
