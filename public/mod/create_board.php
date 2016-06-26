<?php
require '../../res/config.php';
require $config['root'] . '/res/twig_loader.php';

// Set variables to be used more than once from user submission.
$uri = $_POST['uri'];
$title = $_POST['title'];
$subtitle = $_POST['subtitle'];

// Prepare query to insert new board into db.
$query = "insert into boards (uri, title, subtitle) values (:uri, :title, :subtitle)";
$query = $db->prepare($query);

// Bind user submitted variables to query.
$query->bindParam(':uri', $uri);
$query->bindParam(':title', $title);
$query->bindParam(':subtitle', $subtitle);

// Execute the query.
$query->execute();

// Create directory for board files to be stored in.
if (!file_exists($config['root'] . "/public/$uri")) {
    mkdir($config['root'] . "/public/$uri", 0777, true);
}

// Create json file with general information. 
$index_json = $twig->render('board_index.json', array('uri' => $uri, 'title' => $title, 'subtitle' => $subtitle));
$file_index_json = fopen($config['root'] . "/public/$uri/index.json", "w");
fwrite($file_index_json, $index_json);
fclose($file_index_json);

// Create board index.
copy($config['root'] . "/templates/board_index.php", $config['root'] . "/public/$uri/index.php");

header("Location: /$uri");
?>
