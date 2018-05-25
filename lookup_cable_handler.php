<?php
require_once("Model/cable.php");
$id = $_GET['id'];
if(!isset($id) || !$id || $id == '' || strlen($id) != 4) {
    exit;
}
$cable = Cable::retrieveFromDatabase($id);
$cable_arr = array();
$cable_arr['id'] = $cable->getID();
$cable_arr['description'] = $cable->getDescription();
$cable_arr['notes'] = $cable->getNotes();
echo '{';
echo '"cable":'.json_encode($cable_arr);
echo '}';