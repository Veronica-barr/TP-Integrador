<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'sis_facturacionbd';
    private $username = 'root';
    private $password = 'ZEUS123araceli';
    private $conn;

      public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name, 
                $this->username, 
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
         $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            error_log("Error de conexión: " . $exception->getMessage());
            throw new Exception("Error al conectar con la base de datos");
        }

        return $this->conn;
    }
}