<?php
require_once("Model/database.php");
require_once("Model/cable.php");
class Item {
    // protected $item_id;
    protected $inventory_id;
    protected $description;
    protected $model_number;
    protected $serial_number;
    protected $mac_address;
    protected $notes;
    protected $location_id;

    static private function pad_and_pack($str) {
        return pack("H*", str_pad($str, 6, "0", STR_PAD_LEFT));
    }

    static private function unpack($str) {
        $to_return = unpack("H*", $str);
        return $to_return[1];
    }

    function __construct($inventory_id, $description, $model_number, $serial_number, $mac_address, $notes, $location_id) { //, $item_id=Null
        // $this->item_id = $item_id;
        $this->inventory_id = $inventory_id;
        $this->description = $description;
        $this->model_number = $model_number;
        $this->serial_number = $serial_number;
        $this->mac_address = $mac_address;
        $this->notes = $notes;
        $this->location_id = $location_id;
        if($this->location_id == '') {
            $this->location_id = NULL;
        }
    }

    function save() {
        $database = Database::createConnection();
        $stmt = $database->prepare("UPDATE `items` SET `description`=?, `model_number`=?, `serial_number`=?, `mac_address`=?, `notes`=?, `location`=? WHERE `inventory_id`=? LIMIT 1");
        $inventory_id = Item::pad_and_pack($this->inventory_id);
        $stmt->bind_param("sssssss", $this->description, $this->model_number, $this->serial_number, $this->mac_address, $this->notes, $this->location_id, $inventory_id);
        $stmt->execute();
        if($stmt->error != '') {
            throw new Exception('Error saving item.');
        }

    }

    static function retrieveFromDatabase($inventory_id) {
        $inventory_id = Item::pad_and_pack($inventory_id);
        $database = Database::createConnection();
        $stmt = $database->prepare("SELECT `inventory_id`, `description`, `model_number`, `serial_number`, `mac_address`, `notes`, `location` FROM `items` WHERE `inventory_id`=?");
        $stmt->bind_param("s", $inventory_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($inventory_id_from_db, $description, $model_number, $serial_number, $mac_address, $notes, $location);
        $stmt->fetch();
        if($stmt->num_rows != 1) {
            trigger_error("Could not retrieve item from database.", E_USER_NOTICE);
            return Null;
        }
        $unpacked_inventory_id = Item::unpack($inventory_id_from_db);
        return new Item($unpacked_inventory_id, $description, $model_number, $serial_number, $mac_address, $notes, $location);
    }

    static function retrieveAllItemsFromDatabase() {
        $items = array();
        $database = Database::createConnection();
        $stmt = $database->prepare("SELECT `inventory_id` FROM `items`");
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($inventory_id);
        while($stmt->fetch()) {
            $items[] = Item::retrieveFromDatabase(Item::unpack($inventory_id));
        }
        return $items;
    }

    static function createItem($inventory_id, $description, $model_number, $serial_number, $mac_address, $notes, $location) {
        // If $inventory_id is NULL or empty string we will generate one
        if(strlen($inventory_id) > 6) {
            throw new Exception('Inventory ID must be 6 or fewer characters.');
        }
        $database = Database::createConnection();
        if($inventory_id != NULL && $inventory_id != '') {
            $stmt = $database->prepare("INSERT INTO `items` (`inventory_id`, `description`, `model_number`, `serial_number`, `mac_address`, `notes`, `location`) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", Item::pad_and_pack($inventory_id), $description, $model_number, $serial_number, $mac_address, $notes, $location);
            if(!$stmt->execute()) {
                throw new Exception('Could not execute MySQL query.');
            }
            return $inventory_id;
        } else{
            $max_insertion_tries = 10;
            do {
                $stmt = $database->prepare(
                    "SELECT IFNULL((SELECT CONV((CONV(HEX(i.inventory_id), 16, 10) + 1),10,16)
                    FROM items i
                    LEFT JOIN items i1 ON CONV(HEX(i1.inventory_id), 16, 10) = CONV(HEX(i.inventory_id), 16, 10) + 1
                    WHERE i1.inventory_id IS NULL
                    ORDER BY i.inventory_id
                    LIMIT 0, 1), 0) AS `first_available_id`"
                );
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($next_inventory_id);
                $stmt->fetch();

                $stmt = $database->prepare("INSERT INTO `items` (`inventory_id`, `description`, `model_number`, `serial_number`, `mac_address`, `notes`, `location`) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssss", Item::pad_and_pack($next_inventory_id), $description, $model_number, $serial_number, $mac_address, $notes, $location);

                $max_insertion_tries --;
            }
            while(!$stmt->execute() && $max_insertion_tries >= 0);
            if($stmt->error != '') {
                throw new Exception('Could not create item. MySQL error: '.$stmt->error);
            }
            return $next_inventory_id;
        }
    }

    function getInventoryID() {
        return $this->inventory_id;
    }

    function getDescription() {
        return $this->description;
    }
    function setDescription($description) {
        $this->description = $description;
    }

    function getModelNumber() {
        return $this->model_number;
    }
    function setModelNumber($model_number) {
        $this->model_number = $model_number;
    }

    function getSerialNumber() {
        return $this->serial_number;
    }
    function setSerialNumber($serial_number) {
        $this->serial_number = $serial_number;
    }

    function getMacAddress() {
        return $this->mac_address;
    }
    function setMacAddress($mac_address) {
        $this->mac_address = $mac_address;
    }

    function getNotes() {
        return $this->notes;
    }
    function setNotes($notes) {
        $this->notes = $notes;
    }

    function getLocation() {
        return $this->location_id;
    }
    function setLocation($location) {
        $this->location_id = $location;
        if($this->location_id == '') {
            $this->location_id = NULL;
        }
    }

    static function searchForItem($query) {
        $items = array();
        $database = Database::createConnection();
        $packed_query = pack("H*", $query);
        $stmt = $database->prepare("SELECT `inventory_id`, `description` FROM `items` WHERE `inventory_id` LIKE '%$packed_query%'");
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($inventory_id, $description);
        while($stmt->fetch()) {
            $items[] = [Item::unpack($inventory_id), $description];
        }
        return $items;
    }

    static function getItemsConnectedToCable($cable_id) {
        $cable_ids_to_lookup = array();
        $cables_to_lookup = Cable::getCablesConnectedToCable($cable_id);
        foreach($cables_to_lookup as $cable_to_lookup) {
            $cable_ids_to_lookup[] = $cable_to_lookup->getID();
        }
        $cable_ids_to_lookup[] = $cable_id;
        $arr = array();
        $database = Database::createConnection();
        $stmt = $database->prepare("SELECT `item_inventory_id` FROM `items_cables` WHERE `cable_id`=?");
        foreach($cable_ids_to_lookup as $cable_to_lookup) {
            $cable_id = pack("H*", str_pad($cable_to_lookup, 4, "0", STR_PAD_LEFT));
            $stmt->bind_param("s", $cable_id);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($id);
            while($stmt->fetch()) {
                $arr[] = Item::retrieveFromDatabase(Item::unpack($id));
            }
        }

        return $arr;
    }

    static function getAllItemsInLocation($location_id) {
        $items = array();
        $database = Database::createConnection();
        $stmt = $database->prepare("SELECT `inventory_id` FROM `items` WHERE `location`=?");
        $stmt->bind_param("s", $location_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($inventory_id);
        while($stmt->fetch()) {
            $items[] = Item::retrieveFromDatabase(Item::unpack($inventory_id));
        }
        return $items;
    }
}