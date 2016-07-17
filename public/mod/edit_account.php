<?php
require '../../res/config.php';
require $config['root'] . '/public/mod/mod_logout.php';

// Check for valid login before doing anything.
session_start();
if (!empty($_SESSION['permission'])) {
    $orig_username = $_POST['orig_username'];
    $orig_password = $_POST['orig_password'];
    $new_username = $_POST['new_username'];
    if ($orig_password != $_POST['new_password']) {
        $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    }
    else {
        $new_password = $orig_password;
    }

    $query = $db->prepare("update users set username = :new_username, password = :new_password where username = :orig_username");
    $query->bindValue(':new_username', $new_username);
    $query->bindValue(':new_password', $new_password);
    $query->bindvalue(':orig_username', $orig_username);
    $query->execute();

    mod_logout();
    header("Location: /mod.php");
}
else {
    echo "Invalid login.";
}
?>
