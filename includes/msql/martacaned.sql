-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-02-2026 a las 13:27:59
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `martacaned`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `imagen_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `imagen_url`) VALUES
(1, 'Pizzas', 'Hechas al horno de leña', NULL),
(2, 'Entrantes', 'Aperitivos para compartir', 'img/categorias/entrantes.jpg'),
(3, 'Hamburguesas', 'Hamburguesas gourmet con pan brioche', 'img/categorias/hamburguesas.jpg'),
(4, 'Pastas', 'Pastas frescas italianas', 'img/categorias/pastas.jpg'),
(5, 'Ensaladas', 'Ensaladas frescas y saludables', 'img/categorias/ensaladas.jpg'),
(6, 'Postres', 'Postres caseros', 'img/categorias/postres.jpg'),
(7, 'Bebidas', 'Refrescos y bebidas variadas', 'img/categorias/bebidas.jpg'),
(8, 'Bebidas Alcohólicas', 'Cervezas y vinos', 'img/categorias/alcohol.jpg'),
(9, 'Menús', 'Menús combinados especiales', 'img/categorias/menus.jpg'),
(10, 'Especialidades', 'Platos especiales de la casa', 'img/categorias/especialidades.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lineas_pedido`
--

CREATE TABLE `lineas_pedido` (
  `id_pedido` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `numero_pedido` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `fecha_hora` datetime DEFAULT current_timestamp(),
  `tipo` enum('Local','Llevar') NOT NULL,
  `estado` enum('Recibido','En preparación','Cocinando','Listo cocina','Terminado','Entregado') DEFAULT 'Recibido',
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio_base` decimal(10,2) NOT NULL,
  `iva` enum('4','10','21') NOT NULL,
  `disponible` tinyint(1) DEFAULT 1,
  `ofertado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `id_categoria`, `nombre`, `descripcion`, `precio_base`, `iva`, `disponible`, `ofertado`) VALUES
(1, 1, 'Pizza Margherita', 'Tomate, mozzarella y albahaca', 7.27, '10', 1, 1),
(2, 1, 'Pizza Barbacoa', 'Carne picada, salsa BBQ y mozzarella', 9.50, '10', 1, 1),
(3, 1, 'Pizza Cuatro Quesos', 'Mozzarella, gorgonzola, parmesano y cheddar', 10.00, '10', 1, 1),
(4, 1, 'Pizza Pepperoni', 'Pepperoni americano y mozzarella', 9.00, '10', 1, 1),
(5, 2, 'Patatas Bravas', 'Patatas fritas con salsa brava casera', 4.50, '10', 1, 1),
(6, 2, 'Alitas de Pollo', 'Alitas crujientes con salsa BBQ', 6.00, '10', 1, 1),
(7, 2, 'Nachos con Queso', 'Nachos con cheddar fundido y guacamole', 5.50, '10', 1, 1),
(8, 3, 'Hamburguesa Clásica', 'Carne de ternera, lechuga y tomate', 8.50, '10', 1, 1),
(9, 3, 'Hamburguesa Doble', 'Doble carne y doble queso', 10.50, '10', 1, 1),
(10, 3, 'Hamburguesa BBQ', 'Carne, bacon y salsa barbacoa', 9.50, '10', 1, 1),
(11, 4, 'Spaghetti Carbonara', 'Pasta con salsa carbonara tradicional', 8.00, '10', 1, 1),
(12, 4, 'Lasaña Boloñesa', 'Lasaña casera con carne y bechamel', 9.00, '10', 1, 1),
(13, 5, 'Ensalada César', 'Lechuga, pollo, parmesano y salsa César', 7.00, '10', 1, 1),
(14, 5, 'Ensalada Mediterránea', 'Tomate, queso feta y aceitunas', 6.50, '10', 1, 1),
(15, 6, 'Tarta de Queso', 'Tarta casera con mermelada de frutos rojos', 4.50, '10', 1, 1),
(16, 6, 'Brownie con Helado', 'Brownie caliente con bola de vainilla', 5.00, '10', 1, 1),
(17, 7, 'Coca-Cola', 'Refresco de cola 33cl', 2.50, '10', 1, 1),
(18, 7, 'Agua Mineral', 'Botella 50cl', 1.80, '10', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol` enum('cliente','camarero','cocinero','gerente') DEFAULT 'cliente',
  `avatar_tipo` enum('defecto','galeria','subido') DEFAULT 'defecto',
  `avatar_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `email`, `nombre`, `apellidos`, `password_hash`, `rol`, `avatar_tipo`, `avatar_url`) VALUES
(4, 'pp', 'pprez@gmail.com', 'Juan', 'Perez', '$2y$10$kQzq5rlv.7G3DMQEUer7tO0U0sEGCgIuXZWFF2yVTIQDee9JLbnze', 'cliente', 'defecto', 'defecto.png');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `lineas_pedido`
--
ALTER TABLE `lineas_pedido`
  ADD PRIMARY KEY (`id_pedido`,`id_producto`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `lineas_pedido`
--
ALTER TABLE `lineas_pedido`
  ADD CONSTRAINT `lineas_pedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id`),
  ADD CONSTRAINT `lineas_pedido_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
