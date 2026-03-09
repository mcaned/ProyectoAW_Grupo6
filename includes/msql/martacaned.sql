
INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `imagen_url`) VALUES
(1, 'Pizzas', 'Hechas al horno de leña', 'categorias/pizzas.jpg'),
(2, 'Entrantes', 'Aperitivos para compartir', 'categorias/entrantes.jpg'),
(3, 'Hamburguesas', 'Hamburguesas gourmet con pan brioche', 'categorias/hamburguesas.jpg'),
(4, 'Pastas', 'Pastas frescas italianas', 'categorias/pastas.jpg'),
(5, 'Ensaladas', 'Ensaladas frescas y saludables', 'categorias/ensaladas.jpg'),
(6, 'Postres', 'Postres caseros', 'categorias/postres.jpg'),
(7, 'Bebidas', 'Refrescos y bebidas variadas', 'categorias/bebidas.jpg'),
(8, 'Bebidas Alcohólicas', 'Cervezas y vinos', 'categorias/alcohol.jpg'),
(9, 'Menús', 'Menús combinados especiales', 'categorias/menus.jpg'),
(10, 'Especialidades', 'Platos especiales de la casa', 'categorias/especialidades.jpg');


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


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
