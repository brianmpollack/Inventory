<?php
require_once("Model/database.php");
class CableCable {
    protected $id;
    protected $cable_id_1;
    protected $cable_id_2;

    static private function pad_and_pack($str) {
        return pack("H*", str_pad($str, 4, "0", STR_PAD_LEFT));
    }

    static private function unpack($str) {
        $to_return = unpack("H*", $str);
        return $to_return[1];
    }

    function __construct($id, $cable_id_1, $cable_id_2) {
        $this->id = $id;
        $this->cable_id_1 = $cable_id_1;
        $this->cable_id_2 = $cable_id_2;
    }

    static function retrieveFromDatabase($id) {
        $database = Database::createConnection();
        $stmt = $database->prepare("SELECT `link_id`, `cable_id_1`, `cable_id_2` FROM `cables_cables` WHERE `link_id`=?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($link_id_from_db, $cable_id_1, $cable_id_1);
        $stmt->fetch();
        if($stmt->num_rows != 1) {
            trigger_error("Could not retrieve cable from database.", E_USER_NOTICE);
            return Null;
        }
        $cable_id_1 = CableCable::unpack($cable_id_1);
        $cable_id_2 = CableCable::unpack($cable_id_2);
        return new CableCable($link_id_from_db, $cable_id_1, $cable_id_2);
    }

    static function getAllForCable($cable_id) {
        $arr = array();
        $database = Database::createConnection();
        $stmt = $database->prepare("SELECT `link_id` FROM `cables_cables` WHERE `cable_id_1`=? OR `cable_id_2`=?");
        $cable_id = CableCable::pad_and_pack($cable_id);
        $stmt->bind_param("ss", $cable_id, $cable_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id);
        while($stmt->fetch()) {
            $arr[] = CableCable::retrieveFromDatabase($id);
        }
        return $arr;
    }

    static function create($cable_id_1, $cable_id_2) {
        $database = Database::createConnection();
        $stmt = $database->prepare("INSERT INTO `cables_cables` (`cable_id_1`, `cable_id_2`) VALUES (?, ?)");
        $cable_id_1 = CableCable::pad_and_pack($cable_id_1);
        $cable_id_2 = CableCable::pad_and_pack($cable_id_2);
        $stmt->bind_param("ss", $cable_id_1, $cable_id_2);
        if(!$stmt->execute()) {
            throw new Exception('Could not execute MySQL query.');
        }
    }

    function getID() {
        return $this->id;
    }

    function getCableID1() {
        return $this->cable_id_1;
    }

    function getCableID2() {
        return $this->cable_id_2;
    }
}