<?php
require_once("Model/database.php");
class Transaction {
    protected $id;
    protected $item_id;
    protected $date;
    protected $location;
    protected $price;
    protected $notes;

    static private function pad_and_pack_item($str) {
        return pack("H*", str_pad($str, 6, "0", STR_PAD_LEFT));
    }
    static private function unpack_item($str) {
        $to_return = unpack("H*", $str);
        return $to_return[1];
    }

    function __construct($id, $item_id, $date, $location, $price, $notes) {
        $this->id = $id;
        $this->item_id = $item_id;
        $this->date = $date;
        $this->location = $location;
        $this->price = $price;
        $this->notes = $notes;
    }

    function save() {
        $database = Database::createConnection();
        $stmt = $database->prepare("UPDATE `transactions` SET `date`=?, `location`=?, `price`=?, `notes`=? WHERE `transaction_id`=? LIMIT 1");
        $stmt->bind_param("sssss", $this->date, $this->location, $this->price, $this->notes, $this->id);
        $stmt->execute();
        if($stmt->error != '') {
            throw new Exception('Error saving transaction.');
        }
    }

    function delete() {
        $database = Database::createConnection();
        $stmt = $database->prepare("DELETE FROM `transactions` WHERE `transaction_id`=? LIMIT 1");
        $stmt->bind_param("s", $this->id);
        $stmt->execute();
        if($stmt->error != '') {
            throw new Exception('Error deleting transaction.');
        }
    }

    static function retrieveFromDatabase($id) {
        $database = Database::createConnection();
        $stmt = $database->prepare("SELECT `transaction_id`, `item_id`, `date`, `location`, `price`, `notes` FROM `transactions` WHERE `transaction_id`=?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id_from_db, $item_id, $date, $location, $price, $notes);
        $stmt->fetch();
        if($stmt->num_rows != 1) {
            trigger_error("Could not retrieve location from database.", E_USER_NOTICE);
            return Null;
        }
        return new Transaction($id_from_db, Transaction::unpack_item($item_id), $date, $location, $price, $notes);
    }

    static function retrieveAllForItem($item_id) {
        $arr = array();
        $database = Database::createConnection();
        $stmt = $database->prepare("SELECT `transaction_id` FROM `transactions` WHERE `item_id`=?");
        $item_id = Transaction::pad_and_pack_item($item_id);
        $stmt->bind_param("s", $item_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id);
        while($stmt->fetch()) {
            $arr[] = Transaction::retrieveFromDatabase($id);
        }
        return $arr;
    }

    static function createTransaction($item_id, $date, $location, $price, $notes) {
        $database = Database::createConnection();
        $stmt = $database->prepare("INSERT INTO `transactions` (`item_id`, `date`, `location`, `price`, `notes`) VALUES (?, ?, ?, ?, ?)");
        $item_id = Transaction::pad_and_pack_item($item_id);
        $stmt->bind_param("sssss", $item_id, $date, $location, $price, $notes);
        if(!$stmt->execute()) {
            throw new Exception('Could not execute MySQL query.');
        }
    }

    function getID() {
        return $this->id;
    }

    function getItemID() {
        return $this->item_id;
    }

    function getDate() {
        return $this->date;
    }
    function setDate($date) {
        $this->date = $date;
    }

    function getLocation() {
        return $this->location;
    }
    function setLocation($location) {
        $this->location = $location;
    }

    function getPrice() {
        return $this->price;
    }
    function setPrice($price) {
        $this->price = $price;
    }

    function getNotes() {
        return $this->notes;
    }
    function setNotes($notes) {
        $this->notes = $notes;
    }
}