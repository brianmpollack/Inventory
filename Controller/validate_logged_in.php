<?php
require_once("Controller/start_session.php");
if(!isset($_SESSION['user_id'])) {
    header("Location: /");
}
