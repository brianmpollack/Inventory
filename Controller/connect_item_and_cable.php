<?php
require_once("Model/item-cable.php");
require_once("Model/cable-cable.php");
if(isset($_POST['submit']) && $_POST['submit'] == 'save-item-cable') {
    $item_inventory_id = $_POST['item_id'];
    $cable_id = $_POST['cable_id'];

    if(strlen($cable_id) != 4) {
        throw new Exception("Cable ID length not correct.");
    }

    if(strlen($item_inventory_id) == 4) {
        try {
            CableCable::create($item_inventory_id, $cable_id);
        } catch(Exception $e) {
            $user_error = "Could not save.<br>".$e->getMessage();
        }
    } else if(strlen($item_inventory_id) == 6) {
        try {
            ItemCable::create($item_inventory_id, $cable_id);
        } catch(Exception $e) {
            $user_error = "Could not save.<br>".$e->getMessage();
        }
    } else {
        throw new Exception("ID length not correct.");
    }
}