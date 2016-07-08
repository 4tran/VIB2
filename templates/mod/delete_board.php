<?php
// Get list of boards.
$query = $db->prepare("select uri, title from boards");
$query->execute();
$boards = $query->fetchAll();

// Render template.
echo $twig->render('mod/delete_board.html', array('boards' => $boards));
?>
