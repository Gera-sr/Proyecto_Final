<?php
require_once __DIR__ . '/basiccrud.php';
$dbConnCreator = new myConnexion('localhost', 'proyecto', 'root', '', 3306);
$conn = $dbConnCreator->connect();
?>