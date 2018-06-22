<?php
require_once("Model/database.php");
class Maintenance {
    protected $id;
    protected $item_id;
    protected $due_date;
    protected $notes;
    protected $completed;

    static private function pad_and_pack_item($str) {
        return pack("H*", str_pad($str, 6, "0", STR_PAD_LEFT));
    }
    static private function unpack_item($str) {
        $to_return = unpack("H*", $str);
        return $to_return[1];
    }

    function __construct($id, $item_id, $due_date, $notes, $completed) {
        $this->id = $id;
        $this->item_id = $item_id;
        $this->due_date = $due_date;
        $this->notes = $notes;
        $this->completed = $completed;
    }

    function save() {
        $database = Database::createConnection();
        $stmt = $database->prepare("UPDATE `maintenance` SET `due_date`=?, `notes`=?, `completed`=? WHERE `maintenance_id`=? LIMIT 1");
        $stmt->bind_param("ssss", $this->due_date, $this->notes, $this->completed, $this->id);
        $stmt->execute();
        if($stmt->error != '') {
            throw new Exception('Error saving.');
        }
    }

    function delete() {
        $database = Database::createConnection();
        $stmt = $database->prepare("DELETE FROM `maintenance` WHERE `maintenance_id`=? LIMIT 1");
        $stmt->bind_param("s", $this->id);
        $stmt->execute();
        if($stmt->error != '') {
            throw new Exception('Error deleting.');
        }
    }

    static function retrieveFromDatabase($id) {
        $database = Database::createConnection();
        $stmt = $database->prepare("SELECT `maintenance_id`, `item_id`, `due_date`, `notes`, `completed` FROM `maintenance` WHERE `maintenance_id`=?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id_from_db, $item_id, $due_date, $notes, $completed);
        $stmt->fetch();
        if($stmt->num_rows != 1) {
            trigger_error("Could not retrieve from database.", E_USER_NOTICE);
            return Null;
        }
        return new Maintenance($id_from_db, Maintenance::unpack_item($item_id), $due_date, $notes, $completed);
    }

    static function retrieveAllForItem($item_id) {
        $arr = array();
        $database = Database::createConnection();
        $stmt = $database->prepare("SELECT `maintenance_id` FROM `maintenance` WHERE `item_id`=?");
        $item_id = Maintenance::pad_and_pack_item($item_id);
        $stmt->bind_param("s", $item_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id);
        while($stmt->fetch()) {
            $arr[] = Maintenance::retrieveFromDatabase($id);
        }
        return $arr;
    }

    static function createMaintenance($item_id, $due_date, $notes) {
        $database = Database::createConnection();
        $stmt = $database->prepare("INSERT INTO `maintenance` (`item_id`, `due_date`, `notes`) VALUES (?, ?, ?)");
        $item_id = Maintenance::pad_and_pack_item($item_id);
        $stmt->bind_param("sss", $item_id, $due_date, $notes);
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

    function getDueDate() {
        return $this->due_date;
    }
    function setDueDate($due_date) {
        $this->due_date = $due_date;
    }

    function getNotes() {
        return $this->notes;
    }
    function setNotes($notes) {
        $this->notes = $notes;
    }

    function getCompleted() {
        return $this->completed == true;
    }
    function setCompleted($completed) {
        $this->completed = $completed == true;
    }
}