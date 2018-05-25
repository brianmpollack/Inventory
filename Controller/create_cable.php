<?php
require_once("Model/cable.php");
if(isset($_POST['submit']) && $_POST['submit'] == 'new-cable') {
    $prefill_cable_id = $cable_id = $_POST['cable_id'];
    $prefill_description = $description = $_POST['description'];
    $prefill_notes = $notes = $_POST['notes'];


    if($description == "") {
        trigger_error("Description is required.");
    }

    try {
        $id = Cable::createCable($cable_id, $description, $notes);
        header("Location: ./view_cable.php?id=".$id);
    } catch(Exception $e) {
        $user_error = "Could not create cable.<br>".$e->getMessage();
    }
}