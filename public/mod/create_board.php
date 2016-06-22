<?php
// For some reason, I PHP doesn't like required files more than one directory back, so I've redefined $config in this file.
$config = json_decode(file_get_contents("../../config.json"), true);
if ($config['pass'] == 'password') {
  echo 'Please configure your config.json. If, when you refresh this page, you get a 500 error, that means you have misconfigured your config.php.';
}
else {
  $db = new PDO('mysql:host=' . $config['ip'] . ';dbname=' . $config['db'] . ';charset=utf8mb4', $config['user'], $config['pass']);
}

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
?>
