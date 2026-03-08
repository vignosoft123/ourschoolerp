<?php
define('BASEPATH', 'dummy');
require_once 'mvc/config/development/database.php';
$conn = mysqli_connect($db['default']['hostname'], $db['default']['username'], $db['default']['password'], $db['default']['database']);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$result = mysqli_query($conn, "SELECT * FROM usertype");
while($row = mysqli_fetch_assoc($result)) {
    echo "ID: " . $row['usertypeID'] . " | Role: " . $row['usertype'] . "\n";
}

mysqli_close($conn);
