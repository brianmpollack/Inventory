<?php
require_once("Model/location.php");
if(isset($_POST['submit']) && $_POST['submit'] == 'new-location') {
    $prefill_location_id = $location_id = $_POST['id'];
    $prefill_name = $name = $_POST['name'];


    if($name == "") {
        trigger_error("Name is required.");
    }

    try {
        Location::createLocation($name);
        header("Location: ./locations.php");
    } catch(Exception $e) {
        $user_error = "Could not create location.<br>".$e->getMessage();
    }
}