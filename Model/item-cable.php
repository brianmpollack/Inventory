<?php
require_once("Model/database.php");
class ItemCable {
    protected $id;
    protected $item_inventory_id;
    protected $cable_id;

    static private function pad_and_pack($str) {
        return pack("H*", str_pad($str, 4, "0", STR_PAD_LEFT));
    }

    static private function unpack($str) {
        $to_return = unpack("H*", $str);
        return $to_return[1];
    }

    function __construct($id, $item_inventory_id, $cable_id) {
        $this->id = $id;
        $this->item_inventory_id = $item_inventory_id;
        $this->cable_id = $cable_id;
    }

    static function retrieveFromDatabase($id) {
        $database = Database::createConnection();
        $stmt = $database->prepare("SELECT `link_id`, `item_inventory_id`, `cable_id` FROM `items_cables` WHERE `link_id`=?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($link_id_from_db, $item_inventory_id, $cable_id);
        $stmt->fetch();
        if($stmt->num_rows != 1) {
            trigger_error("Could not retrieve cable from database.", E_USER_NOTICE);
            return Null;
        }
        $unpacked_item_inventory_id = ItemCable::unpack($item_inventory_id);
        $unpacked_cable_id = ItemCable::unpack($cable_id);
        return new ItemCable($link_id_from_db, $unpacked_item_inventory_id, $unpacked_cable_id);
    }

    static function retrieveAllFromDatabase() {
        $items_cables = array();
        $database = Database::createConnection();
        $stmt = $database->prepare("SELECT `link_id` FROM `items_cables`");
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id);
        while($stmt->fetch()) {
            $items_cables[] = ItemCable::retrieveFromDatabase($id);
        }
        return $items_cables;
    }

    static function getAllWithItem($item_inventory_id) {
        $items_cables = array();
        $database = Database::createConnection();
        $stmt = $database->prepare("SELECT `link_id` FROM `items_cables` WHERE `item_inventory_id`=?");
        $item_inventory_id = ItemCable::pad_and_pack($item_inventory_id);
        $stmt->bind_param("s", $item_inventory_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id);
        while($stmt->fetch()) {
            $items_cables[] = ItemCable::retrieveFromDatabase($id);
        }
        return $items_cables;
    }

    static function getAllWithCable($cable_id) {
        $items_cables = array();
        $database = Database::createConnection();
        $stmt = $database->prepare("SELECT `link_id` FROM `items_cables` WHERE `cable_id`=?");
        $stmt->bind_param("s", ItemCable::pad_and_pack($cable_id));
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id);
        while($stmt->fetch()) {
            $items_cables[] = ItemCable::retrieveFromDatabase($id);
        }
        return $items_cables;
    }

    static function create($item_inventory_id, $cable_id) {
        $database = Database::createConnection();
        $stmt = $database->prepare("INSERT INTO `items_cables` (`item_inventory_id`, `cable_id`) VALUES (?, ?)");
        $item_inventory_id = ItemCable::pad_and_pack($item_inventory_id);
        $cable_id = ItemCable::pad_and_pack($cable_id);
        $stmt->bind_param("ss", $item_inventory_id, $cable_id);
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

    function getCableID() {
        return $this->cable_id;
    }
}