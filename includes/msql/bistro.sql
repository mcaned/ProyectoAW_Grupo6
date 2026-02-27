-- 1. Usuarios (Funcionalidad 0)
CREATE TABLE Usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    rol ENUM('cliente', 'camarero', 'cocinero', 'gerente') DEFAULT 'cliente',
    avatar_tipo ENUM('defecto', 'galeria', 'subido') DEFAULT 'defecto',
    avatar_url VARCHAR(255)
);

-- 2. Categorías (Funcionalidad 1)
CREATE TABLE Categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    imagen_url VARCHAR(255)
);

-- 3. Productos (Funcionalidad 1)
CREATE TABLE Productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_categoria INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio_base DECIMAL(10,2) NOT NULL,
    iva ENUM('4', '10', '21') NOT NULL,
    disponible BOOLEAN DEFAULT TRUE,
    ofertado BOOLEAN DEFAULT TRUE, -- Esto es el borrado lógico
    FOREIGN KEY (id_categoria) REFERENCES Categorias(id)
);

-- 4. Pedidos (Funcionalidad 2)
-- Nota: "Nuevo" y "Cancelado" no se guardan en BD según el enunciado.
CREATE TABLE Pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_pedido INT NOT NULL, -- Se debe reiniciar cada día por código
    id_cliente INT NOT NULL,
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    tipo ENUM('Local', 'Llevar') NOT NULL,
    estado ENUM('Recibido', 'En preparación', 'Cocinando', 'Listo cocina', 'Terminado', 'Entregado') DEFAULT 'Recibido',
    total DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_cliente) REFERENCES Usuarios(id)
);

-- 5. Líneas de Pedido (Los productos dentro de un pedido)
CREATE TABLE Lineas_Pedido (
    id_pedido INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    PRIMARY KEY (id_pedido, id_producto),
    FOREIGN KEY (id_pedido) REFERENCES Pedidos(id),
    FOREIGN KEY (id_producto) REFERENCES Productos(id)
);SELECT * FROM `categorias` WHERE 1
