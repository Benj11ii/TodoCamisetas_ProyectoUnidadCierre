<?php
// models/Cliente.php
require_once __DIR__ . '/../config/database.php';

class Cliente {

    public static function all(): array {
        try {
            $db = Database::getConnection();
            $stmt = $db->query("SELECT * FROM clientes");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error en Cliente::all(): " . $e->getMessage()]);
            exit;
        }
    }

    public static function find(int $id): ?array {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT * FROM clientes WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch();
            return $result ? $result : null;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error en Cliente::find(): " . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Busca un cliente por su nombre comercial (ej: '90minutos').
     * Esencial para calcular los precios de oferta dinámicos.
     */
    public static function findByComercialId(string $comercialId): ?array {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT * FROM clientes WHERE nombre_comercial = :nombre_comercial");
            $stmt->execute([':nombre_comercial' => $comercialId]);
            $result = $stmt->fetch();
            return $result ? $result : null;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error en Cliente::findByComercialId(): " . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Se debe comprobar si un cliente tiene camisetas asociadas antes de eliminarlo.
     */
    public static function hasAssociatedCamisetas(int $id): bool {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT COUNT(*) as total FROM camisetas WHERE cliente_id = :cliente_id");
            $stmt->execute([':cliente_id' => $id]);
            $row = $stmt->fetch();
            return (int)$row['total'] > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Crea un nuevo cliente usando transacciones.
     */
    public static function create(array $data): bool {
        $db = Database::getConnection();
        try {
            $db->beginTransaction();

            $stmt = $db->prepare("INSERT INTO clientes (nombre_comercial, rut, direccion, categoria, contacto, porcentaje_oferta) 
                                  VALUES (:nombre_comercial, :rut, :direccion, :categoria, :contacto, :porcentaje_oferta)");
            
            $stmt->execute([
                ':nombre_comercial' => $data['nombre_comercial'],
                ':rut' => $data['rut'],
                ':direccion' => $data['direccion'],
                ':categoria' => $data['categoria'],
                ':contacto' => $data['contacto'],
                ':porcentaje_oferta' => $data['porcentaje_oferta'] ?? 0.00
            ]);

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }

    /**
     * Actualizar  los datos de un cliente.
     */
    public static function update(int $id, array $data): bool {
        $db = Database::getConnection();
        try {
            $db->beginTransaction();

            $stmt = $db->prepare("UPDATE clientes SET 
                                    nombre_comercial = :nombre_comercial, 
                                    rut = :rut, 
                                    direccion = :direccion, 
                                    categoria = :categoria, 
                                    contacto = :contacto, 
                                    porcentaje_oferta = :porcentaje_oferta 
                                  WHERE id = :id");
            
            $stmt->execute([
                ':id' => $id,
                ':nombre_comercial' => $data['nombre_comercial'],
                ':rut' => $data['rut'],
                ':direccion' => $data['direccion'],
                ':categoria' => $data['categoria'],
                ':contacto' => $data['contacto'],
                ':porcentaje_oferta' => $data['porcentaje_oferta'] ?? 0.00
            ]);

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }

    /**
     * Eliminar un cliente.
     */
    public static function delete(int $id): bool {
        $db = Database::getConnection();
        try {
            $db->beginTransaction();

            $stmt = $db->prepare("DELETE FROM clientes WHERE id = :id");
            $stmt->execute([':id' => $id]);

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }
}