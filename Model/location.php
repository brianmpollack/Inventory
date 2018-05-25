<?php
require_once("Model/database.php");
class Location {
    protected $id;
    protected $name;

    function __construct($id, $name) {
        $this->id = $id;
        $this->name = $name;
    }

    function save() {
        $database = Database::createConnection();
        $stmt = $database->prepare("UPDATE `locations` SET `name`=? WHERE `location_id`=? LIMIT 1");
        $stmt->bind_param("ss", $this->name, $this->id);
        $stmt->execute();
        if($stmt->error != '') {
            throw new Exception('Error saving location.');
        }

    }

    static function retrieveFromDatabase($id) {
        $database = Database::createConnection();
        $stmt = $database->prepare("SELECT `location_id`, `name` FROM `locations` WHERE `location_id`=?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id_from_db, $name);
        $stmt->fetch();
        if($stmt->num_rows != 1) {
            trigger_error("Could not retrieve location from database.", E_USER_NOTICE);
            return Null;
        }
        return new Location($id_from_db, $name);
    }

    static function retrieveAllFromDatabase() {
        $locations = array();
        $database = Database::createConnection();
        $stmt = $database->prepare("SELECT `location_id` FROM `locations`");
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id);
        while($stmt->fetch()) {
            $locations[] = Location::retrieveFromDatabase($id);
        }
        return $locations;
    }

    static function createLocation($id) {
        $database = Database::createConnection();
        $stmt = $database->prepare("INSERT INTO `locations` (`name`) VALUES (?)");
        $stmt->bind_param("s", $id);
        if(!$stmt->execute()) {
            throw new Exception('Could not execute MySQL query.');
        }
    }

    function getID() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }
    function setName($name) {
        $this->name = $name;
    }
}