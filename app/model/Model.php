<?php
require_once __DIR__ . '/../../config/db.php';

class Model {
    protected $conn;

    public function __construct($conn = null) {
        if ($conn) {
            $this->conn = $conn;
        } else {
            global $db;
            $this->conn = $db;
        }
    }
}
?>
