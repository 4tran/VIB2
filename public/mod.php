<?php
require '../res/config.php';
require $config['root'] . '/res/twig_loader.php';

// Log in
session_start();

if ($_SESSION['admin'] == "false" || $_SESSION['admin'] == NULL) {
    echo $twig->render('mod/mod_login.html', array('title' => $config['site_name']));
}

// If logged in, load the available options.
if ($_SESSION['admin'] == "true") {
    echo $twig->render('mod/options.html');

    if (isset($_GET['create_board'])) {
        echo $twig->render('mod/create_board.html');
    }
    if (isset($_GET['delete_board'])) {
        include $config['root'] . '/templates/mod/delete_board.php';
    }
}
?>
