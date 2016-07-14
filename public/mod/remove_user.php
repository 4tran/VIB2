<?php
require '../../res/config.php';

$username = $_POST['username'];

$query = $db->prepare("delete from users where username = :username");
$query->bindValue(':username', $username);
$query->execute();

header("Location: /mod.php");
?>
