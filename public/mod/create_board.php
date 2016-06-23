<?php
require '../../res/config.php';
require '../../res/twig_loader.php';

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

// Create directories and index files.
if (!file_exists("../$uri")) {
    mkdir("../$uri", 0777, true);
}
?>
