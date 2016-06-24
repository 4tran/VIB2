<?php
require_once '../res/config.php';
require_once $config['root'] . '/res/twig_loader.php';

// Log in
if ($_COOKIE['admin']){
    echo $twig->render('mod/mod_login.html', array('title' => $config['site_name']));
}

if (isset($_GET['create_board'])) {
  echo $twig->render('mod/create_board.html', array('title' => $config['site_name']));
}
?>
<html>
<head>
  <title>Mod panel</title>
</head>
<body>
<a href='mod.php?create_board'>Create a new board</a>
</body>
</html>
