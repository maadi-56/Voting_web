<?php
$conn = mysqli_connect("localhost", "root", "", "voting_system");

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

// No 'else' block with a die() here. 
// The script will now quietly connect and continue loading your pages!
?>