<?php
// index.php
header("Content-Type: application/json; charset=UTF-8");

// Configuración de CORS básico para permitir conexiones externas
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Manejo de peticiones preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Tabla de enrutamiento basada en Expresiones Regulares
// Los paréntesis () capturan variables dinámicas como IDs, que serán enviadas al controlador
$routes = [
    'GET' => [
        '/^\/api\/camisetas$/' => 'CamisetaController::index',
        '/^\/api\/camisetas\/([0-9]+)$/' => 'CamisetaController::show',
        '/^\/api\/clientes$/' => 'ClienteController::index',
        '/^\/api\/clientes\/([0-9]+)$/' => 'ClienteController::show',
        '/^\/api\/camisetas\/([0-9]+)\/tallas$/' => 'TallaController::index',
    ],
    'POST' => [
        '/^\/api\/camisetas$/' => 'CamisetaController::store',
        '/^\/api\/clientes$/' => 'ClienteController::store',
        '/^\/api\/camisetas\/([0-9]+)\/tallas$/' => 'TallaController::store',
    ],
    'PUT' => [
        '/^\/api\/camisetas\/([0-9]+)$/' => 'CamisetaController::update',
        '/^\/api\/clientes\/([0-9]+)$/' => 'ClienteController::update',
    ],
    'DELETE' => [
        '/^\/api\/camisetas\/([0-9]+)$/' => 'CamisetaController::destroy',
        '/^\/api\/clientes\/([0-9]+)$/' => 'ClienteController::destroy',
        '/^\/api\/camisetas\/([0-9]+)\/tallas\/([0-9]+)$/' => 'TallaController::destroy',
    ]
];

$matched = false;

if (isset($routes[$method])) {
    foreach ($routes[$method] as $pattern => $handler) {
        if (preg_match($pattern, $requestUri, $matches)) {
            $matched = true;
            
            // Remover la coincidencia  completa para quedarnos solo con las variables dinámicas
            array_shift($matches); 
            
            // Separar el controlador del método a llamar
            list($controllerClass, $actionMethod) = explode('::', $handler);
            
            // Cargar  dinámicamente el archivo del controlador correspondiente
            $controllerFile = __DIR__ . "/controllers/{$controllerClass}.php";
            
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                
                // Ejecutar el método estático del controlador  pasando los parámetros dinámicos
                call_user_func_array([$controllerClass, $actionMethod], $matches);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "El controlador {$controllerClass} no existe en el servidor."]);
            }
            break;
        }
    }
}

if (!$matched) {
    http_response_code(404);
    echo json_encode([
        "error" => "Ruta no encontrada o método HTTP no permitido",
        "metodo_recibido" => $method,
        "ruta_recibida" => $requestUri
    ]);
}