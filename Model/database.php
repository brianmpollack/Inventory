<?php
class Database {
    private static $database = NULL;

    function __construct ($connection) {
        $this->connection = $connection;
    }

    function __destruct () {
        $this->connection->close();
    }

    function prepare($statement) {
        if(!$stmt = $this->connection->prepare($statement)) {
            trigger_error("Database error: ".$this->connection->error);
        }
        return $stmt;
    }

    function begin_transaction() {
        return $this->connection->begin_transaction();
    }
    function commit() {
        return $this->connection->commit();
    }

    public static function createConnection() {
        if(!Database::$database) {
            require('./database_credentials.php');

            $new_connection = new mysqli($db_hostname, $db_username, $db_password, $db_database);
            if($new_connection->connect_error) {
                trigger_error("Error. Could not connect to database. Error number: ".$new_connection->connect_errorno, E_USER_ERROR);
            }
            Database::$database = new Database($new_connection);
        }
		return Database::$database;
    }
}