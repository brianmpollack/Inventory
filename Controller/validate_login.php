<?php
require_once("Model/database.php");
if(isset($_POST['username']) && isset($_POST['password'])) {
    $database = Database::createConnection();
    $stmt = $database->prepare("SELECT `user_id`, `password` FROM `login` WHERE `username`=?");
    $stmt->bind_param("s", $_POST['username']);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $hashed_password);
    $stmt->fetch();
    if(isset($user_id) && isset($hashed_password)) {
        echo $user_id;
        echo $hashed_password;
        if(password_verify($_POST['password'], $hashed_password)) {
            require_once("Controller/start_session.php");
            $_SESSION['user_id'] = $user_id;
            header("Location: items.php");
        }
    }
}
