<?php
require '../../res/config.php';

$username = $_POST['username'];
$password = $_POST['password'];

$query = $db->prepare("select * from users");
$query->execute();
$users = $query->fetchAll();

session_start();

if ($username == $users[0]['username'] && password_verify($password, $users[0]['password'])) {
    $_SESSION['admin'] = 'true';
    header("Location: /mod.php");
}
else {
    $_SESSION['admin'] = 'false';
    echo "Incorrect username or password";
}
?>
