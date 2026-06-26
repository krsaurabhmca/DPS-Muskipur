<?php
// Database Credentials
$host     = "localhost"; 
$username = "u673864504_dps_2627";     // your database username
$password = "@Dps_2001";    // your database password
$database = "u673864504_dps_2627";     // your database name

// Create Connection for mysqli
$conn = new mysqli($host, $username, $password, $database);

// Check Connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Optional: Set UTF-8 Encoding
$conn->set_charset("utf8");

class Database {
    private $host = "localhost";
    private $db_name = "u673864504_dps_2627";
    private $username = "u673864504_dps_2627";
    private $password = "@Dps_2001";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>
