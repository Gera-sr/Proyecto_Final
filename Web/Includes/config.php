<?php
require_once __DIR__ . '/basiccrud.php';
$dbConnCreator = new myConnexion('localhost', 'Proyecto', 'root', '', 3306);
$conn = $dbConnCreator->connect();
?>