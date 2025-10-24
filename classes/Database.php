<?php
/**
 * Database Connection Class
 * 
 * Handles all database operations for the affiliate system.
 * This class provides methods for connecting, reading, and saving data.
 */

class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $db = "calamus_db";
    
    // Production database settings (commented out)
    // private $host = "82.180.143.139";
    // private $username = "u608908096_kht_navy";
    // private $password = "kHt_5241";
    // private $db = "u608908096_easyenglish";

    /**
     * Establish database connection
     * 
     * @return mysqli|false Database connection or false on failure
     */
    public function connect() {
        $connection = mysqli_connect($this->host, $this->username, $this->password, $this->db);
        
        if (!$connection) {
            error_log("Database connection failed: " . mysqli_connect_error());
            return false;
        }
        
        // Set charset to UTF-8
        mysqli_set_charset($connection, "utf8");
        
        return $connection;
    }

    /**
     * Execute SELECT query and return results
     * 
     * @param string $query SQL SELECT query
     * @return array|false Array of results or false on failure
     */
    public function read($query) {
        $conn = $this->connect();
        
        if (!$conn) {
            return false;
        }
        
        $result = mysqli_query($conn, $query);
        
        if (!$result) {
            error_log("Database read error: " . mysqli_error($conn));
            mysqli_close($conn);
            return false;
        }
        
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        
        mysqli_free_result($result);
        mysqli_close($conn);
        
        return $data;
    }

    /**
     * Execute INSERT, UPDATE, DELETE queries
     * 
     * @param string $query SQL query
     * @return bool True on success, false on failure
     */
    public function save($query) {
        $conn = $this->connect();
        
        if (!$conn) {
            return false;
        }
        
        $result = mysqli_query($conn, $query);
        
        if (!$result) {
            error_log("Database save error: " . mysqli_error($conn));
            mysqli_close($conn);
            return false;
        }
        
        mysqli_close($conn);
        return true;
    }
    
    /**
     * Get the last inserted ID
     * 
     * @return int|string Last inserted ID
     */
    public function getLastInsertId() {
        $conn = $this->connect();
        
        if (!$conn) {
            return false;
        }
        
        $lastId = mysqli_insert_id($conn);
        mysqli_close($conn);
        
        return $lastId;
    }
    
    /**
     * Escape string to prevent SQL injection
     * 
     * @param string $string String to escape
     * @return string Escaped string
     */
    public function escape($string) {
        $conn = $this->connect();
        
        if (!$conn) {
            return false;
        }
        
        $escaped = mysqli_real_escape_string($conn, $string);
        mysqli_close($conn);
        
        return $escaped;
    }
    
    /**
     * Execute a prepared statement
     * 
     * @param string $query SQL query with placeholders
     * @param array $params Parameters to bind
     * @return array|false Results or false on failure
     */
    public function preparedQuery($query, $params = []) {
        $conn = $this->connect();
        
        if (!$conn) {
            return false;
        }
        
        $stmt = mysqli_prepare($conn, $query);
        
        if (!$stmt) {
            error_log("Prepared statement error: " . mysqli_error($conn));
            mysqli_close($conn);
            return false;
        }
        
        if (!empty($params)) {
            $types = str_repeat('s', count($params)); // All strings for now
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        
        return $data;
    }
}
?>
