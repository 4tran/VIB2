<?php
require '../res/config.php';
require $config['root'] . '/res/twig_loader.php';
require $config['root'] . '/public/mod/mod_logout.php';

// Log in
session_start();
$username = $_SESSION['username'];
$password = $_SESSION['password'];
$permission = $_SESSION['permission'];

if (empty($permission)) {
    echo $twig->render('mod/mod_login.html', array('title' => $config['site_name']));
}

// If logged in, load the available options.
if (!empty($permission)) {
    // All users
    echo $twig->render('mod/options.html', array('permission' => $_SESSION['permission']));

    if (isset($_GET['edit_account'])) {
        echo $twig->render('mod/edit_account.html', array('username' => $username, 'password' => $password));
    }
    if (isset($_GET['mod_logout'])) {
        mod_logout();
        header("Location: /mod.php");
    }
    
    // Admin options
    if ($permission == 'admin') {
        if (isset($_GET['create_board'])) {
            echo $twig->render('mod/create_board.html');
        }
        if (isset($_GET['delete_board'])) {
            include $config['root'] . '/templates/mod/delete_board.php';
        }
        if (isset($_GET['add_user'])) {
            echo $twig->render('mod/add_user.html');
        }
        if (isset($_GET['remove_user'])) {
            include $config['root'] . '/templates/mod/remove_user.php';
        }
    }

    // Moderator options
}
?>
