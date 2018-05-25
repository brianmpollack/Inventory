<?php
require_once("Model/item.php");
if(isset($_POST['submit']) && $_POST['submit'] == 'new-item') {
    $prefill_inventory_id = $inventory_id = $_POST['inventory_id'];
    $prefill_description = $description = $_POST['description'];
    $prefill_model = $model_number = $_POST['model'];
    $prefill_serial = $serial_number = $_POST['serial'];
    $prefill_mac_address = $mac_address = $_POST['mac_address'];
    $prefill_notes = $notes = $_POST['notes'];


    if($description == "") {
        trigger_error("Description is required.");
    }

    try {
        $inventory_id = Item::createItem($inventory_id, $description, $model_number, $serial_number, $mac_address, $notes);
        header("Location: ./view_item.php?inventory_id=".$inventory_id);
    } catch(Exception $e) {
        $user_error = "Could not create item.<br>".$e->getMessage();
    }
}