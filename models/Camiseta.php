<?php
// models/Camiseta.php
require_once __DIR__ . '/../config/database.php';

class Camiseta {

    public static function all(): array {
        try {
            $db = Database::getConnection();
            $stmt = $db->query("SELECT * FROM camisetas");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error en Camiseta::all(): " . $e->getMessage()]);
            exit;
        }
    }

    public static function find(int $id): ?array {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT * FROM camisetas WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch();
            return $result ? $result : null;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error en Camiseta::find(): " . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Insertar una nueva camiseta usando transacciones
     */
    public static function create(array $data): bool {
        $db = Database::getConnection();
        try {
            $db->beginTransaction(); // Transacción explícita

            $stmt = $db->prepare("INSERT INTO camisetas (cliente_id, titulo, club, pais, tipo, color, precio, precio_oferta, detalles, codigo_producto) 
                                  VALUES (:cliente_id, :titulo, :club, :pais, :tipo, :color, :precio, :precio_oferta, :detalles, :codigo_producto)");
            
            $stmt->execute([
                ':cliente_id' => $data['cliente_id'],
                ':titulo' => $data['titulo'],
                ':club' => $data['club'],
                ':pais' => $data['pais'],
                ':tipo' => $data['tipo'],
                ':color' => $data['color'],
                ':precio' => $data['precio'],
                ':precio_oferta' => $data['precio_oferta'] ?? null,
                ':detalles' => $data['detalles'] ?? null,
                ':codigo_producto' => $data['codigo_producto']
            ]);

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }

    /**
     * Actualizar una camiseta existente usando transacciones.
     */
    public static function update(int $id, array $data): bool {
        $db = Database::getConnection();
        try {
            $db->beginTransaction();

            $stmt = $db->prepare("UPDATE camisetas SET 
                                    cliente_id = :cliente_id,
                                    titulo = :titulo, 
                                    club = :club, 
                                    pais = :pais, 
                                    tipo = :tipo, 
                                    color = :color, 
                                    precio = :precio, 
                                    precio_oferta = :precio_oferta, 
                                    detalles = :detalles, 
                                    codigo_producto = :codigo_producto 
                                  WHERE id = :id");
            
            $stmt->execute([
                ':id' => $id,
                ':cliente_id' => $data['cliente_id'],
                ':titulo' => $data['titulo'],
                ':club' => $data['club'],
                ':pais' => $data['pais'],
                ':tipo' => $data['tipo'],
                ':color' => $data['color'],
                ':precio' => $data['precio'],
                ':precio_oferta' => $data['precio_oferta'] ?? null,
                ':detalles' => $data['detalles'] ?? null,
                ':codigo_producto' => $data['codigo_producto']
            ]);

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }
    /**
     * Eliminar una camiseta.
     */
    public static function delete(int $id): bool {
        $db = Database::getConnection();
        try {
            $db->beginTransaction();

            $stmt = $db->prepare("DELETE FROM camisetas WHERE id = :id");
            $stmt->execute([':id' => $id]);

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }
}