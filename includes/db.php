<?php
$servername = "localhost";
$username = "suivi_user";
$password = "AdeZ878c*ZZ4d#ec@rz8q";
$dbname = "user_time_data";

$db = new mysqli($servername, $username, $password, $dbname);

if ($db->connect_error) {
    die("Connexion échouée: " . $db->connect_error);
}

