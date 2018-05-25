<?php
require_once("Model/database.php");
class Cable {
    protected $id;
    protected $description;
    protected $notes;

    static private function pad_and_pack($str) {
        return pack("H*", str_pad($str, 4, "0", STR_PAD_LEFT));
    }

    static private function unpack($str) {
        $to_return = unpack("H*", $str);
        return $to_return[1];
    }

    function __construct($id, $description, $notes) {
        $this->id = $id;
        $this->description = $description;
        $this->notes = $notes;
    }

    function save() {
        $database = Database::createConnection();
        $stmt = $database->prepare("UPDATE `cables` SET `description`=?, `notes`=? WHERE `cable_id`=? LIMIT 1");
        $id = Cable::pad_and_pack($this->id);
        $stmt->bind_param("sss", $this->description, $this->notes, $id);
        $stmt->execute();
        if($stmt->error != '') {
            throw new Exception('Error saving cable.');
        }

    }

    static function retrieveFromDatabase($id) {
        $id = Cable::pad_and_pack($id);
        $database = Database::createConnection();
        $stmt = $database->prepare("SELECT `cable_id`, `description`, `notes` FROM `cables` WHERE `cable_id`=?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($cable_id_from_db, $description, $notes);
        $stmt->fetch();
        if($stmt->num_rows != 1) {
            trigger_error("Could not retrieve cable from database.", E_USER_NOTICE);
            return Null;
        }
        $unpacked_id_id = Cable::unpack($cable_id_from_db);
        return new Cable($unpacked_id_id, $description, $notes);
    }

    static function retrieveAllFromDatabase() {
        $cables = array();
        $database = Database::createConnection();
        $stmt = $database->prepare("SELECT `cable_id` FROM `cables`");
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id);
        while($stmt->fetch()) {
            $cables[] = Cable::retrieveFromDatabase(Cable::unpack($id));
        }
        return $cables;
    }

    static function createCable($id, $description, $notes) {
        // If $id is NULL or empty string we will generate one
        if(strlen($id) > 4) {
            throw new Exception('ID must be 4 or fewer characters.');
        }
        $database = Database::createConnection();
        if($id != NULL && $id != '') {
            $stmt = $database->prepare("INSERT INTO `cables` (`cable_id`, `description`, `notes`) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", Cable::pad_and_pack($id), $description, $notes);
            if(!$stmt->execute()) {
                throw new Exception('Could not execute MySQL query.');
            }
        } else{
            $max_insertion_tries = 10;
            do {
                $stmt = $database->prepare(
                    "SELECT IFNULL((SELECT CONV((CONV(HEX(i.cable_id), 16, 10) + 1),10,16)
                    FROM cables i
                    LEFT JOIN cables i1 ON CONV(HEX(i1.cable_id), 16, 10) = CONV(HEX(i.cable_id), 16, 10) + 1
                    WHERE i1.cable_id IS NULL
                    ORDER BY i.cable_id
                    LIMIT 0, 1), 0) AS `first_available_id`"
                );
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($next_id);
                $stmt->fetch();

                $stmt = $database->prepare("INSERT INTO `cables` (`cable_id`, `description`, `notes`) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", Item::pad_and_pack($next_id), $description, $notes);

                $max_insertion_tries --;
            }
            while(!$stmt->execute() && $max_insertion_tries >= 0);
            if($stmt->error != '') {
                throw new Exception('Could not create cable. MySQL error: '.$stmt->error);
            }
        }
    }

    function getID() {
        return $this->id;
    }

    function getDescription() {
        return $this->description;
    }
    function setDescription($description) {
        $this->description = $description;
    }

    function getNotes() {
        return $this->notes;
    }
    function setNotes($notes) {
        $this->notes = $notes;
    }
}