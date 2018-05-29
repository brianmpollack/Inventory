<?php
require_once("Model/cable.php");
if(isset($_POST['submit']) && $_POST['submit'] == 'save-cable') {
    $id = $_POST['cable_id'];
    $description = $_POST['description'];
    $notes = $_POST['notes'];

    try {
        $cable = Cable::retrieveFromDatabase($id);
        $cable->setDescription($description);
        $cable->setNotes($notes);
        $cable->save();
    } catch(Exception $e) {
        $user_error = "Could not save cable.<br>".$e->getMessage();
    }
}