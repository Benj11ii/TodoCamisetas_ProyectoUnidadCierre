-- Crear tabla de Clientes B2B
CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_comercial VARCHAR(100) NOT NULL,
    rut VARCHAR(20) NOT NULL UNIQUE,
    direccion VARCHAR(255) NOT NULL,
    categoria ENUM('Regular', 'Preferencial') NOT NULL DEFAULT 'Regular',
    contacto VARCHAR(150) NOT NULL,
    porcentaje_oferta DECIMAL(5,2) DEFAULT 0.00
);

-- Crear tabla de Camisetas (Stock)
CREATE TABLE IF NOT EXISTS camisetas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL, -- Validación de cliente para evitar eliminación si está asociado a camiseta
    titulo VARCHAR(150) NOT NULL,
    club VARCHAR(100) NOT NULL,
    pais VARCHAR(100) NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    color VARCHAR(100) NOT NULL,
    precio INT NOT NULL,
    precio_oferta INT NULL,
    detalles TEXT NULL,
    codigo_producto VARCHAR(50) NOT NULL UNIQUE,
    
    -- RESTICCIÓN DE INTEGRIDAD REFERENCIAL
    -- ON DELETE RESTRICT bloquea la eliminación del cliente si tiene camisetas
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE RESTRICT
);

-- Crear tabla de Tallas
CREATE TABLE IF NOT EXISTS tallas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(10) NOT NULL UNIQUE
);

-- Crear tabla intermedia Muchos a Muchos (M:N) con ON DELETE CASCADE
CREATE TABLE IF NOT EXISTS camiseta_tallas (
    camiseta_id INT NOT NULL,
    talla_id INT NOT NULL,
    PRIMARY KEY (camiseta_id, talla_id),
    FOREIGN KEY (camiseta_id) REFERENCES camisetas(id) ON DELETE CASCADE,
    FOREIGN KEY (talla_id) REFERENCES tallas(id) ON DELETE CASCADE
);

-- Insertar las tallas básicas de forma inicial
INSERT IGNORE INTO tallas (id, nombre) VALUES 
(1, 'S'), 
(2, 'M'), 
(3, 'L'), 
(4, 'XL');