# TodoCamisetas API

API RESTful desarrollada en **PHP puro (sin frameworks)**, dockerizada con **Nginx** y **MySQL 8.0**.  
Gestiona el inventario de camisetas de fútbol y los clientes B2B de la empresa TodoCamisetas.

---

## Stack Tecnológico

| Componente | Tecnología |

| Lenguaje | PHP 8.x (puro, sin frameworks) |
| Servidor web | Nginx |
| Base de datos | MySQL 8.0 |
| Entorno | Docker / Docker Compose |
| Documentación | OpenAPI 3.0 (Swagger) |

---

## Arquitectura del Proyecto (MVC)

```
backend/
├── config/
│   └── database.php          # Conexión PDO Singleton a MySQL
├── controllers/
│   ├── CamisetaController.php # CRUD de camisetas y lógica de precios
│   ├── ClienteController.php  # CRUD de clientes B2B
│   └── TallaController.php    # CRUD de tallas (relación M:N)
├── models/
│   ├── Camiseta.php           # Consultas preparadas PDO — camisetas
│   ├── Cliente.php            # Consultas preparadas PDO — clientes
│   └── Talla.php              # Consultas preparadas PDO — tallas
├── routes/
│   └── router.php             # Enrutador con expresiones regulares
├── docker/
│   └── nginx.conf             # Configuración del servidor web
├── index.php                  # Front Controller (punto de entrada único)
├── docker-compose.yml
├── database.sql               # Esquema de tablas e integridad referencial
├── swagger.yaml               # Especificación OpenAPI
└── EVA3_CarmonaBenjamin.postman_collection.json
```

---

## Requisitos

- **Docker Desktop** instalado y en ejecución.

---

## Instalación y Ejecución Local

### 1. Levantar los contenedores

Desde la carpeta raíz del proyecto (`backend/`):

```bash
docker compose up -d --build
```

### 2. Importar el esquema de base de datos

Una vez que los contenedores estén activos:

```bash
docker exec -i todocamisetas_db mysql -uroot -psecret_password todocamisetas < database.sql
```

La API quedará disponible en: **`http://localhost:8080`**

---

## Endpoints Principales

| Método | Ruta | Descripción |

| GET | `/api/camisetas` | Listar todas las camisetas |
| GET | `/api/camisetas/{id}?cliente_id={nombre}` | Obtener camiseta con precio dinámico |
| POST | `/api/camisetas` | Registrar nueva camiseta |
| PUT | `/api/camisetas/{id}` | Actualizar camiseta |
| DELETE | `/api/camisetas/{id}` | Eliminar camiseta |
| GET | `/api/clientes` | Listar clientes B2B |
| POST | `/api/clientes` | Registrar nuevo cliente |
| DELETE | `/api/clientes/{id}` | Eliminar cliente (bloqueado si tiene camisetas) |
| POST | `/api/camisetas/{id}/tallas` | Asociar talla a camiseta (M:N) |
| DELETE | `/api/camisetas/{id}/tallas/{t_id}` | Desasociar talla |

> La documentación completa de cada endpoint (parámetros, request/response y errores) está disponible en `swagger.yaml`.

---

## Documentación y Pruebas

- **Swagger / OpenAPI:** Importar `swagger.yaml` en [swagger.io](https://editor.swagger.io) para visualizar todos los endpoints.
- **Postman:** Importar `EVA3_CarmonaBenjamin.postman_collection.json` para ejecutar las pruebas con ejemplos de request y response incluidos.

---

## Autor

**Benjamín Alonso Carmona Vega**  
Desarrollo Backend — Sección 52  
Instituto Profesional San Sebastián  
Profesor: Patricio Eduardo Silva
