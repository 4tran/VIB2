<?php
// Get list of users
$query = $db->prepare("select username from users");
$query->execute();
$users = $query->fetchAll();

echo $twig->render('mod/remove_user.html', array('users' => $users));
?>
