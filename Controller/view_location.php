<?php
if(isset($_GET['id'])) {
    $location = Location::retrieveFromDatabase($_GET['id']);
    if($location != NULL) {
        $prefill_location_id = $location->getID();
        $prefill_name = $location->getName();
        $items = Item::getAllItemsInLocation($location->getID());
    }
}