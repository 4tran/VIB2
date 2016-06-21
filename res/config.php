<?php
$config = json_decode(file_get_contents("../config.json"), true);
if ($config['pass'] == 'password') {
  echo 'Please configure your config.json. If, when you refresh this page, you get a 500 error, that means you have misconfigured your config.php.';
}
else {
  $db = new PDO('mysql:host=' . $config['ip'] . ';dbname=' . $config['db'] . ';charset=utf8mb4', $config['user'], $config['pass']);
}
?>
