<?php
class Database {
    private static $instance = null;

    public static function getConnection() {
        if (self::$instance === null) {
            $host = "localhost";
            $dbname = "sis_facturacionbd";
            $username = "root";
            $password = "ZEUS123araceli";
            self::$instance = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$instance;
    }
}
?>
