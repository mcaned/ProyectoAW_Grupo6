<?php
class GestorOfertas {
    public static function calcularAhorro($carrito, $idOfertas) {
        if (empty($idOfertas) || empty($carrito)) return 0;
        
        $idOfertas = is_array($idOfertas) ? $idOfertas : [$idOfertas];
        $inventario = $carrito; 
        $ahorroAcumulado = 0;
        $cacheProductos = [];

        foreach ($idOfertas as $idO) {
            $oferta = Oferta::buscaPorId($idO);
            if (!$oferta) continue;

            $productosOferta = $oferta->getProductos(); 
            if (empty($productosOferta)) continue;

            $aplicarNuevamente = true;

            while ($aplicarNuevamente) {
                foreach ($productosOferta as $item) {
                    $idP = $item->getIdProducto();
                    $cantidadNecesaria = $item->getCantidad();
                    
                    if ($cantidadNecesaria <= 0 || ($inventario[$idP] ?? 0) < $cantidadNecesaria) {
                        $aplicarNuevamente = false;
                        break;
                    }
                }

                if ($aplicarNuevamente) {
                    $precioPackSinDescuento = 0;
                    foreach ($productosOferta as $item) {
                        $idP = $item->getIdProducto();
                        if (!isset($cacheProductos[$idP])) {
                            $cacheProductos[$idP] = Producto::buscaPorId($idP);
                        }
                        
                        $p = $cacheProductos[$idP];
                        if ($p) {
                            $precioPackSinDescuento += $p->getPrecioFinal() * $item->getCantidad();
                            $inventario[$idP] -= $item->getCantidad();
                        }
                    }
                    $ahorroAcumulado += ($precioPackSinDescuento * ($oferta->getDescuento() / 100));
                }
            }
        }
        return round($ahorroAcumulado, 2);
    }
}