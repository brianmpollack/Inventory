<?php
require_once("Model/item.php");
$id = $_GET['id'];
if(!isset($id) || !$id || $id == '' || strlen($id) != 6) {
    exit;
}
$item = Item::retrieveFromDatabase($id);
$item_arr = array();
$item_arr['inventory_id'] = $item->getInventoryID();
$item_arr['description'] = $item->getDescription();
$item_arr['model_number'] = $item->getModelNumber();
$item_arr['serial_number'] = $item->getSerialNumber();
$item_arr['mac_address'] = $item->getMacAddress();
$item_arr['notes'] = $item->getNotes();
echo '{';
echo '"item":'.json_encode($item_arr);
echo '}';