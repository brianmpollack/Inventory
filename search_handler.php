<?php
require_once("Model/item.php");
require_once("Model/cable.php");
$query = $_GET['q'];
if(!isset($query) || !$query || $query == '') {
    exit;
}
$found_items = Item::searchForItem($query);
$found_cables = Cable::searchForCable($query);
echo '{';
echo '"Items":'.json_encode($found_items).',';
echo '"Cables":'.json_encode($found_cables);
echo '}';