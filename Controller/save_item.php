<?php
require_once("Model/item.php");
if(isset($_POST['submit']) && $_POST['submit'] == 'save-item') {
    $inventory_id = $_POST['inventory_id'];
    $description = $_POST['description'];
    $model_number = $_POST['model'];
    $serial_number = $_POST['serial'];
    $mac_address = $_POST['mac_address'];
    $notes = $_POST['notes'];

    if($description == "") {
        trigger_error("Description is required.");
        exit;
    }

    try {
        $item = Item::retrieveFromDatabase($inventory_id);
        $item->setDescription($description);
        $item->setModelNumber($model_number);
        $item->setSerialNumber($serial_number);
        $item->setMacAddress($mac_address);
        $item->setNotes($notes);
        $item->save();
    } catch(Exception $e) {
        $user_error = "Could not save item.<br>".$e->getMessage();
    }
}