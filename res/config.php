<?php
// BE SURE TO MODIFY THIS SECTION TO THE SPECIFICATIONS OF YOUR SITE.
$config = [
    // Database connection info.
    "user" => "root",
    "pass" => "password",
    "db" => "vib",
    "ip" => "localhost",
    
    // Where VIB is installed.
    "root" => "/var/www",
    // What you want your site to be called.
    "site_name" => "VIB"
]; 
if ($config['pass'] == 'password') {
  echo 'Please configure the config in config.php. If, when you refresh this page, you get a 500 error, that means you have misconfigured your config.';
}
else {
  $db = new PDO('mysql:host=' . $config['ip'] . ';dbname=' . $config['db'] . ';charset=utf8mb4', $config['user'], $config['pass']);
}
?>
