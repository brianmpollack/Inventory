<?php
require_once("Model/cable.php");
if(isset($_GET['id'])) {
    $cable = Cable::retrieveFromDatabase($_GET['id']);
    if($cable != NULL) {
        $prefill_cable_id = $cable->getID();
        $prefill_description = $cable->getDescription();
        $prefill_notes = $cable->getNotes();
    }
}