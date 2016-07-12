<?php
require '../../res/config.php';

$username = $_POST['username'];
$password = $_POST['password'];

$query = $db->prepare("select * from users");
$query->execute();
$users = $query->fetchAll();

session_start();
$logged_in = false;

// Check for valid login.
for ($i = 0; $i < count($users); $i++) {
    if ($username == $users[$i]['username'] && password_verify($password, $users[$i]['password'])) {
        $_SESSION['username'] = $users[$i]['permission'];
        $_SESSION['password'] = $users[$i]['password'];
        $_SESSION['permission'] = $users[$i]['permission'];
        $logged_in = true;
        break;
    }
}

if ($logged_in == true) {
    header("Location: /mod.php");
}
else {
    echo "Invalid login.";
}
?>
