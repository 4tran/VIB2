<?php
require '../../res/config.php';

$username = $_POST['username'];
$password = $_POST['password'];

$query = $db->prepare("select * from users");
$query->execute();
$users = $query->fetchAll();

if ($username == $users[0]['username'] && password_verify($password, $users[0]['password'])) {
    setcookie("admin", "true", time()+3600, "/");
    header("Location: /mod.php");
}
else {
    setcookie("admin", "false", time()+3600, "/");
    echo "Incorrect username or password";
}
?>
