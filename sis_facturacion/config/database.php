<?php
class Database {
    // Configuración de la base de datos
    //propiedades privadas con credenciales
    private $host = 'localhost';
    private $db_name = 'sis_facturacionbd';
    private $username = 'root';
    private $password = 'ZEUS123araceli';
    private $conn;

    public function getConnection() {
        $this->conn = null;//inicializa con null para evitar conexiones previas

        try {
            // Crea una nueva conexión PDO
            // PDO extension de php que permite trabajar con bases de datos
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name, 
                $this->username, 
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //lanza excepciones en caso de error
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //devuelve resultados como arrays asociativos
                    PDO::ATTR_EMULATE_PREPARES => false, //desactiva la emulación de sentencias preparadas
                ]
            );
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            error_log("Error de conexión: " . $exception->getMessage());
            throw new Exception("Error al conectar con la base de datos");
        }

        return $this->conn; //devuelve el objeto de conexion 
    }
}
?>