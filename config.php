<?php
// C:\xampp\htdocs\projetweb\Kernel\config.php

// Check if class already exists
if (!class_exists('DatabaseConfig')) {
    class DatabaseConfig {
        private static $connection = null;
        
        public static function getConnection() {
            if (self::$connection === null) {
                $host = "localhost";
                $dbname = "kernel";
                $username = "root";
                $password = "";
                
                try {
                    self::$connection = new PDO(
                        "mysql:host=$host;dbname=$dbname",
                        $username,
                        $password,
                        array(
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                        )
                    );
                } catch (PDOException $e) {
                    die("Database connection failed: " . $e->getMessage());
                }
            }
            return self::$connection;
        }
    }
}
?>