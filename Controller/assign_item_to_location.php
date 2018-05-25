<?php
require_once("Model/item-location.php");
if(isset($_POST['submit']) && $_POST['submit'] == 'save-item-location') {
    $item_inventory_id = $_POST['item_id'];
    $prefill_location_id = $location_id = $_POST['location_id'];

    if(strlen($item_inventory_id) != 6) {
        throw new Exception("ID length not correct.");
    }

    try {
        ItemLocation::create($item_inventory_id, $location_id);
    } catch(Exception $e) {
        $user_error = "Could not save.<br>".$e->getMessage();
    }
}