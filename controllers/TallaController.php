<?php
// controllers/TallaController.php
require_once __DIR__ . '/../models/Talla.php';
require_once __DIR__ . '/../models/Camiseta.php';

class TallaController {

    /**
     * GET /api/camisetas/{camiseta_id}/tallas
     */
    public static function index($camisetaId) {
        $camisetaId = (int)$camisetaId;

        // Validar existencia de camisetaId
        $camiseta = Camiseta::find($camisetaId);
        if (!$camiseta) {
            http_response_code(404); // Not Found
            echo json_encode(["error" => "La camiseta con el ID {$camisetaId} no existe."]);
            return;
        }

        $tallas = Talla::getByCamisetaId($camisetaId);
        http_response_code(200); // OK
        echo json_encode($tallas);
    }

    /**
     * POST /api/camisetas/{camiseta_id}/tallas
     */
    public static function store($camisetaId) {
        $camisetaId = (int)$camisetaId;

        // Validar existencia de la camiseta antes de asociar
        $camiseta = Camiseta::find($camisetaId);
        if (!$camiseta) {
            http_response_code(404); // No encontrada
            echo json_encode(["error" => "La camiseta especificada no existe."]);
            return;
        }

        // Leer datos del JSON
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        // Validar parámetro obligatorio
        if (!isset($data['talla_id']) || empty($data['talla_id'])) {
            http_response_code(400); // Bad Request
            echo json_encode(["error" => "El campo 'talla_id' es obligatorio."]);
            return;
        }

        $tallaId = (int)$data['talla_id'];

        // Intentar asociar en la tabla intermedia
        if (Talla::associate($camisetaId, $tallaId)) {
            http_response_code(201); // Created
            echo json_encode(["message" => "Talla asociada a la camiseta de manera exitosa."]);
        } else {
            http_response_code(400); // Bad Request (por ejemplo, si ya estaba asociada)
            echo json_encode(["error" => "No se pudo asociar la talla. Verifique si ya se encuentra asociada."]);
        }
    }

    /**
     * DELETE /api/camisetas/{camiseta_id}/tallas/{talla_id}
     */
    public static function destroy($camisetaId, $tallaId) {
        $camisetaId = (int)$camisetaId;
        $tallaId = (int)$tallaId;

        // Validar existencia de la camiseta
        $camiseta = Camiseta::find($camisetaId);
        if (!$camiseta) {
            http_response_code(404); // Not Found
            echo json_encode(["error" => "La camiseta especificada no existe."]);
            return;
        }

        // Intentar eliminar la asociación
        if (Talla::disassociate($camisetaId, $tallaId)) {
            http_response_code(200); // OK
            echo json_encode(["message" => "Talla desasociada de la camiseta de manera exitosa."]);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(["error" => "Error interno al intentar desasociar la talla."]);
        }
    }
}