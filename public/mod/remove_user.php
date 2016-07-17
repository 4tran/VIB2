<?php
require '../../res/config.php';

// Check for valid login before doing anything.
session_start();
if ($_SESSION['permission'] == 'admin') {
    $username = $_POST['username'];

    $query = $db->prepare("delete from users where username = :username");
    $query->bindValue(':username', $username);
    $query->execute();

    header("Location: /mod.php");
}
else {
    echo "Invalid login.";
}
?>
