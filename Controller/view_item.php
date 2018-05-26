<?php
require_once('Model/item-cable.php');
if(isset($_GET['inventory_id'])) {
    $item = Item::retrieveFromDatabase($_GET['inventory_id']);
    if($item != NULL) {
        $prefill_inventory_id = $item->getInventoryID();
        $prefill_description = $item->getDescription();
        $prefill_model = $item->getModelNumber();
        $prefill_serial = $item->getSerialNumber();
        $prefill_mac_address = $item->getMacAddress();
        $prefill_notes = $item->getNotes();
        $cable_connections = ItemCable::getAllWithItem($item->getInventoryID());
    }
}