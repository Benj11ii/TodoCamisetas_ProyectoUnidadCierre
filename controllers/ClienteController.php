<?php
// controllers/ClienteController.php
require_once __DIR__ . '/../models/Cliente.php';

class ClienteController {

    /**
     * GET /api/clientes
     */
    public static function index() {
        $clientes = Cliente::all();
        http_response_code(200);
        echo json_encode($clientes);
    }

    /**
     * GET /api/clientes/{id}
     */
    public static function show($id) {
        $id = (int)$id;
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(["error" => "El ID de cliente proporcionado no es válido."]);
            return;
        }

        $cliente = Cliente::find($id);
        if (!$cliente) {
            http_response_code(404);
            echo json_encode(["error" => "El cliente no fue encontrado."]);
            return;
        }

        http_response_code(200);
        echo json_encode($cliente);
    }

    /**
     * POST /api/clientes
     */
    public static function store() {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        // Validación de campos obligatorios  requerida para clientes
        $required = ['nombre_comercial', 'rut', 'direccion', 'categoria', 'contacto'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty(trim((string)$data[$field]))) {
                http_response_code(400);
                echo json_encode(["error" => "El campo '{$field}' es obligatorio."]);
                return;
            }
        }

        // Validar que la categoría ingresada sea la correcta
        if (!in_array($data['categoria'], ['Regular', 'Preferencial'])) {
            http_response_code(400);
            echo json_encode(["error" => "La categoría debe ser únicamente 'Regular' o 'Preferencial'."]);
            return;
        }

        if (Cliente::create($data)) {
            http_response_code(201);
            echo json_encode(["message" => "Cliente registrado exitosamente."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error interno al intentar registrar el cliente."]);
        }
    }

    /**
     * PUT /api/clientes/{id}
     */
    public static function update($id) {
        $id = (int)$id;
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(["error" => "ID no válido para actualización."]);
            return;
        }

        $cliente = Cliente::find($id);
        if (!$cliente) {
            http_response_code(404);
            echo json_encode(["error" => "El cliente que intentas actualizar no existe."]);
            return;
        }

        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        $required = ['nombre_comercial', 'rut', 'direccion', 'categoria', 'contacto'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty(trim((string)$data[$field]))) {
                http_response_code(400);
                echo json_encode(["error" => "El campo '{$field}' es obligatorio para actualizar."]);
                return;
            }
        }

        if (Cliente::update($id, $data)) {
            http_response_code(200);
            echo json_encode(["message" => "Cliente actualizado exitosamente."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error interno al intentar actualizar el cliente."]);
        }
    }

    /**
     * Eliminar  /api/clientes/{id}
     */
    public static function destroy($id) {
        $id = (int)$id;
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(["error" => "ID no válido para la eliminación."]);
            return;
        }

        $cliente = Cliente::find($id);
        if (!$cliente) {
            http_response_code(404);
            echo json_encode(["error" => "El cliente que intentas eliminar no existe."]);
            return;
        }

        // Validación crucial, se debe  evitar eliminación si tiene camisetas asociadas
        if (Cliente::hasAssociatedCamisetas($id)) {
            http_response_code(400); // Retornar un estado 400 Bad Request
            echo json_encode([
                "error" => "No se puede eliminar el cliente porque tiene camisetas asociadas a su cuenta comercial."
            ]);
            return;
        }

        if (Cliente::delete($id)) {
            http_response_code(200);
            echo json_encode(["message" => "Cliente eliminado exitosamente."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error interno al intentar eliminar el cliente."]);
        }
    }
}