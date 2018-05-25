<?php
require_once("Model/location.php");
if(isset($_POST['submit']) && $_POST['submit'] == 'save-location') {
    $location_id = $_POST['location_id'];
    $name = $_POST['name'];

    if($name == "") {
        trigger_error("Name is required.");
        exit;
    }

    try {
        $location = Location::retrieveFromDatabase($location_id);
        $location->setName($name);
        $location->save();
    } catch(Exception $e) {
        $user_error = "Could not save location.<br>".$e->getMessage();
    }
}