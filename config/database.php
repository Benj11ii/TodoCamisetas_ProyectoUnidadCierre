<?php
// config/Database.php

class Database {
    private static $connection = null;

    /**
     * Retorna la conexión única de PDO.
     * @return PDO
     */
    public static function getConnection(): PDO {
        if (self::$connection === null) {
            try {
                // El host es 'db' porque coincide con el nombre del servicio en docker-compose
                $host = 'db';
                $dbname = 'todocamisetas';
                $username = 'root';
                $password = 'secret_password';

                self::$connection = new PDO(
                    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                    $username,
                    $password
                );

                // Configurar manejo de excepciones para errores de SQL
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Configurar el modo de obtención de datos asociativo por defecto
                self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            } catch (PDOException $e) {
                // En caso de fallo en la conexión, se responde con código HTTP 500
                http_response_code(500);
                echo json_encode([
                    "error" => "Error de conexión con la base de datos: " . $e->getMessage()
                ]);
                exit;
            }
        }
        return self::$connection;
    }
}