<?php
require '../../res/config.php';
require $config['root'] . '/res/twig_loader.php';

// Load board config.
$board_config = json_decode(file_get_contents("index.json"), true);

// After all the logic is done, render the index.
echo $twig->render('board_index.html', array(
    'title' => $board_config['title'],
    'subtitle' => $board_config['subtitle']
));
?>
