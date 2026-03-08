<?php
define('BASEPATH', 'dummy');
require_once 'mvc/config/development/database.php';
$conn = mysqli_connect($db['default']['hostname'], $db['default']['username'], $db['default']['password'], $db['default']['database']);
$res = mysqli_query($conn, 'SHOW CREATE TABLE permission_relationships');
$row = mysqli_fetch_assoc($res);
echo $row['Create Table'];
