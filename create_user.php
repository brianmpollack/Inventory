<?php
require_once("Model/database.php");
if(php_sapi_name() != 'cli') {
    echo "User addition can only be done in command line mode.";
    exit;
}
if($argc < 3) {
    echo "Must call with: create_user.php username password";
}
$username = $argv[1];
$password = $argv[2];
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
if(!$hashed_password) {
    echo "Password hash failure.";
    exit;
}
$database = Database::createConnection();
$stmt = $database->prepare("INSERT INTO `login` (`username`, `password`) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $hashed_password);
$stmt->execute();
echo "User added.";