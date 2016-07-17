<?php
require '../../res/config.php';

// Check for valid login before doing anything.
session_start();
if ($_SESSION['permission'] == 'admin') {
    $username = $_POST['username'];
    $permission = $_POST['permission'];
    $password = password_hash("password", PASSWORD_DEFAULT);

    $query = $db->prepare("insert into users (username, password, permission) values (:username, :password, :permission)");
    $query->bindValue(':username', $username);
    $query->bindValue(':password', $password);
    $query->bindValue(':permission', $permission);
    $query->execute();

    header("Location: /mod.php");
}
else {
    echo "Invalid login.";
}
?>
