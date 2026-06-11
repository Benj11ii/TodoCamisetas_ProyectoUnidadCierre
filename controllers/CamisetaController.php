<?php
// controllers/CamisetaController.php
require_once __DIR__ . '/../models/Camiseta.php';

class CamisetaController {

    public static function index() {
        $camisetas = Camiseta::all();
        http_response_code(200);
        echo json_encode($camisetas);
    }

    /**
     * Endpoint: GET /api/camisetas/{id}
     */
    public static function show($id) {
        $id = (int)$id;
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(["error" => "El ID de la camiseta proporcionado no es válido."]);
            return;
        }

        $camiseta = Camiseta::find($id);
        if (!$camiseta) {
            http_response_code(404);
            echo json_encode(["error" => "La camiseta con el ID {$id} no fue encontrada."]);
            return;
        }

        // Calcula el precio dinámico de oferta
        $clienteIdParam = $_GET['cliente_id'] ?? null;
        $precioFinal = $camiseta['precio']; // Precio base por defecto

        if ($clienteIdParam) {
            require_once __DIR__ . '/../models/Cliente.php';
            $cliente = Cliente::findByComercialId($clienteIdParam);

            if ($cliente) {
                // Caso Preferencial: Categoría Preferencial (ej: "90minutos") y precio_oferta definido
                if ($cliente['categoria'] === 'Preferencial' && !is_null($camiseta['precio_oferta'])) {
                    $precioFinal = $camiseta['precio_oferta'];
                }
                // Nota: Si el cliente es Regular (ej: "tdeportes") o no hay precio de oferta,
                // se mantendrá el precio base de la camiseta.
            }
        }

        $camiseta['precio_final'] = (int)$precioFinal;

        // 2. Obtener las tallas asociadas mediante un JOIN
        require_once __DIR__ . '/../models/Talla.php';
        $camiseta['tallas'] = Talla::getByCamisetaId($id);

        http_response_code(200);
        echo json_encode($camiseta);
    }

    /**
     * POST /api/camisetas
     */
    public static function store() {
        // Leer el cuerpo JSON enviado por el cliente
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        // Validación de campos obligatorios
        $required = ['cliente_id', 'titulo', 'club', 'pais', 'tipo', 'color', 'precio', 'codigo_producto'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty(trim((string)$data[$field]))) {
                http_response_code(400);
                echo json_encode(["error" => "El campo '{$field}' es obligatorio."]);
                return;
            }
        }

        // Ejecutar creación
        if (Camiseta::create($data)) {
            http_response_code(201);
            echo json_encode(["message" => "Camiseta creada exitosamente."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error interno al intentar guardar la camiseta."]);
        }
    }

    /**
     * PUT /api/camisetas/{id}
     */
    public static function update($id) {
        $id = (int)$id;
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(["error" => "ID no válido para actualización."]);
            return;
        }

        $camiseta = Camiseta::find($id);
        if (!$camiseta) {
            http_response_code(404);
            echo json_encode(["error" => "La camiseta que intentas actualizar no existe."]);
            return;
        }

        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        // Validación de campos obligatorios
        $required = ['titulo', 'club', 'pais', 'tipo', 'color', 'precio', 'codigo_producto'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty(trim((string)$data[$field]))) {
                http_response_code(400);
                echo json_encode(["error" => "El campo '{$field}' es obligatorio para actualizar."]);
                return;
            }
        }

        if (Camiseta::update($id, $data)) {
            http_response_code(200);
            echo json_encode(["message" => "Camiseta actualizada exitosamente."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error interno al intentar actualizar la camiseta."]);
        }
    }

    /**
     * Para borrar /api/camisetas/{id}
     */
    public static function destroy($id) {
        $id = (int)$id;
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(["error" => "ID no válido para eliminación."]);
            return;
        }

        $camiseta = Camiseta::find($id);
        if (!$camiseta) {
            http_response_code(404);
            echo json_encode(["error" => "La camiseta que intentas eliminar no existe."]);
            return;
        }

        if (Camiseta::delete($id)) {
            http_response_code(200);
            echo json_encode(["message" => "Camiseta eliminada exitosamente."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error interno al intentar eliminar la camiseta."]);
        }
    }
}