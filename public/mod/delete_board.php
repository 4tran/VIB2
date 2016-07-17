<?php
require '../../res/config.php';
require $config['root'] . '/inc/rm-rf.php';

// Check for valid login before doing anything.
session_start();
if ($_SESSION['permission'] == 'admin') {
    // Set variables to be used more than once.
    $uri = $_POST['board'];

    // Delete listing in boards table
    $query = $db->prepare("delete from boards where uri = :uri");
    $query->bindValue(':uri', $uri);
    $query->execute();

    // Delete all posts associated with board.
    $query = $db->prepare("delete from posts where uri = :uri");
    $query->bindValue('uri', $uri);
    $query->execute();

    // Delete directory in which the board was contained
    rmrf($config['root'] . "/public/$uri");

    header("Location: /mod.php?destroy_board");
}
else {
    echo "Invalid login.";
}
?>
