<?php
require_once("Model/database.php");
class ItemLocation {
    protected $id;
    protected $item_inventory_id;
    protected $location_id;

    static private function pad_and_pack($str) {
        return pack("H*", str_pad($str, 4, "0", STR_PAD_LEFT));
    }

    static private function unpack($str) {
        $to_return = unpack("H*", $str);
        return $to_return[1];
    }

    function __construct($id, $item_inventory_id, $location_id) {
        $this->id = $id;
        $this->item_inventory_id = $item_inventory_id;
        $this->location_id = $location_id;
    }

    static function retrieveFromDatabase($id) {
        $database = Database::createConnection();
        $stmt = $database->prepare("SELECT `link_id`, `item_inventory_id`, `location_id` FROM `items_locations` WHERE `link_id`=?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($link_id_from_db, $item_inventory_id, $location_id);
        $stmt->fetch();
        if($stmt->num_rows != 1) {
            trigger_error("Could not retrieve from database.", E_USER_NOTICE);
            return Null;
        }
        $unpacked_item_inventory_id = ItemLocation::unpack($item_inventory_id);
        return new ItemLocation($link_id_from_db, $unpacked_item_inventory_id, $location_id);
    }

    static function retrieveAllFromDatabase() {
        $items_locations = array();
        $database = Database::createConnection();
        $stmt = $database->prepare("SELECT `link_id` FROM `items_locations`");
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id);
        while($stmt->fetch()) {
            $items_locations[] = ItemLocation::retrieveFromDatabase($id);
        }
        return $items_locations;
    }

    static function create($item_inventory_id, $location_id) {
        $database = Database::createConnection();
        $stmt = $database->prepare("INSERT INTO `items_locations` (`item_inventory_id`, `location_id`) VALUES (?, ?)");
        $stmt->bind_param("ss", ItemLocation::pad_and_pack($item_inventory_id), $location_id);
        if(!$stmt->execute()) {
            throw new Exception('Could not execute MySQL query.');
        }
    }

    function getID() {
        return $this->id;
    }

    function getItemInventoryID() {
        return $this->item_inventory_id;
    }

    function getLocationID() {
        return $this->location_id;
    }
}