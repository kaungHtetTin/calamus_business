<?php
require_once __DIR__ . '/../env_loader.php';

/**
 * Database Connection Class
 * 
 * Handles all database operations for the affiliate system.
 * This class provides methods for connecting, reading, and saving data.
 */

class Database {
    private static $connection = null;
    private static $shutdownRegistered = false;
    private $host;
    private $username;
    private $password;
    private $db;

    public function __construct() {
        $this->host = envValue('DB_HOST', 'localhost');
        $this->username = envValue('DB_USERNAME', 'root');
        $this->password = envValue('DB_PASSWORD', '');
        $this->db = envValue('DB_DATABASE', 'calamus_db');
    }

    /**
     * Establish database connection
     * 
     * @return mysqli|false Database connection or false on failure
     */
    public function connect() {
        if (self::$connection instanceof mysqli) {
            return self::$connection;
        }

        $connection = mysqli_connect($this->host, $this->username, $this->password, $this->db);
        
        if (!$connection) {
            error_log("Database connection failed: " . mysqli_connect_error());
            return false;
        }
        
        // Set charset to full UTF-8 support.
        mysqli_set_charset($connection, "utf8mb4");
        
        // Set timezone to UTC for consistency
        mysqli_query($connection, "SET time_zone = '+00:00'");
        
        self::$connection = $connection;

        if (!self::$shutdownRegistered) {
            self::$shutdownRegistered = true;
            register_shutdown_function(function () {
                if (self::$connection instanceof mysqli) {
                    @mysqli_close(self::$connection);
                    self::$connection = null;
                }
            });
        }

        return self::$connection;
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
            return false;
        }
        
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        
        mysqli_free_result($result);
        
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
            return false;
        }
        
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
        
        return $data;
    }
}
?>
