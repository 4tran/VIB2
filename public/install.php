<?php
require '../res/config.php';

// Create table boards
$db->query("create table boards (uri text not null, title text not null, subtitle text not null, post_count int(11) not null)");

// Create table posts. Note that all posts are in one table, rather than each board having its own posts_(uri)
$db->query("create table posts (
  uri text not null,
  id int(11) not null,
  op int(11) not null,
  name text not null,
  content text not null,
  image text not null,
  thumbnail text not null,
  timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  bump timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
)");

// Create table users.
$db->query("create table users (username text not null, password text not null)");

// Generate hashed password. This will be able to be changed later in the mod panel.
$password = password_hash("admin", PASSWORD_DEFAULT); 

// Create new user "admin"
$query = "insert into users (username, password) values ('admin', :password)";
$query = $db->prepare($query);
$query->bindParam(':password', $password);
$query->execute();

?>
