<?php
// models/Talla.php
require_once __DIR__ . '/../config/database.php';

class Talla {

    /**
     * Obtener las tallas asociadas a una camiseta usando un JOIN
     * @param int $camisetaId
     * @return array
     */
    public static function getByCamisetaId(int $camisetaId): array {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT t.id, t.nombre 
                                  FROM tallas t
                                  JOIN camiseta_tallas ct ON t.id = ct.talla_id 
                                  WHERE ct.camiseta_id = :camiseta_id");
            $stmt->execute([':camiseta_id' => $camisetaId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error en Talla::getByCamisetaId(): " . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Asociar una talla a una camiseta (Relación muchos a muchos M:N) //Tabla intermedia
     */
    public static function associate(int $camisetaId, int $tallaId): bool {
        $db = Database::getConnection();
        try {
            $db->beginTransaction();

            $stmt = $db->prepare("INSERT INTO camiseta_tallas (camiseta_id, talla_id) VALUES (:camiseta_id, :talla_id)");
            $stmt->execute([
                ':camiseta_id' => $camisetaId,
                ':talla_id' => $tallaId
            ]);

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }

    /**
     * Desasociar una talla de una camiseta
     */
    public static function disassociate(int $camisetaId, int $tallaId): bool {
        $db = Database::getConnection();
        try {
            $db->beginTransaction();

            $stmt = $db->prepare("DELETE FROM camiseta_tallas WHERE camiseta_id = :camiseta_id AND talla_id = :talla_id");
            $stmt->execute([
                ':camiseta_id' => $camisetaId,
                ':talla_id' => $tallaId
            ]);

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }
}