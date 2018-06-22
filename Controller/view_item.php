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
        $prefill_location = $item->getLocation();
        $items_cables = ItemCable::getAllWithItem($item->getInventoryID());
        $connected_cables = array();
        foreach($items_cables as $item_cable) {
            $connected_cables[] = Cable::retrieveFromDatabase($item_cable->getCableID());
        }
        $transactions = Transaction::retrieveAllForItem($item->getInventoryID());
        $files = $item->getFilenames();
        $maintenance = Maintenance::retrieveAllForItem($item->getInventoryID());
    }
}