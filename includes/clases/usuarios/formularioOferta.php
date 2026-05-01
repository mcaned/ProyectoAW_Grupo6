<?php

require_once __DIR__ . '/../formulario.php';
require_once __DIR__ . '/../aplicacion.php'; 
require_once __DIR__ . '/../ofertas.php';
require_once __DIR__ . '/../producto.php'; 

class FormularioOferta extends formulario {
    private $idOferta;
    private $oferta;

    public function __construct($idOferta = null) {
        $this->idOferta = $idOferta;
        $this->oferta = null;
        
        $action = $_SERVER['PHP_SELF'] . ($idOferta ? "?id=$idOferta" : "");
        parent::__construct('formOferta', ['action' => $action]);
        
        if ($idOferta) {
            $this->oferta = Oferta::buscaPorId($idOferta);
        }
    }

    protected function generaCamposFormulario($datosIniciales) {
        $nombre = $this->oferta ? $this->oferta->getNombre() : '';
        $descripcion = $this->oferta ? $this->oferta->getDescripcion() : '';
        $descuento = $this->oferta ? $this->oferta->getDescuento() : 0;
        
        $comienzo = $this->oferta ? date('Y-m-d', strtotime($this->oferta->getComienzo())) : date('Y-m-d');
        $fin = $this->oferta ? date('Y-m-d', strtotime($this->oferta->getFin())) : date('Y-m-d', strtotime('+7 days'));

        $todosLosProductos = Producto::listar(true); 

        $Productos = "";
        $productosSeleccionados = [];
        if ($this->oferta) {
            foreach ($this->oferta->getProductos() as $op) {
                $productosSeleccionados[$op->getIdProducto()] = $op->getCantidad();
            }
        }

        foreach ($todosLosProductos as $prod) {
            $idProd = $prod->getId();
            $estaSeleccionado = isset($productosSeleccionados[$idProd]);
            $checked = $estaSeleccionado ? 'checked' : '';
            $cantidad = $estaSeleccionado ? $productosSeleccionados[$idProd] : 1;
            $precioIva = number_format($prod->getPrecioFinal(), 2);

            $Productos .= <<<EOF
                <div class = "cabecera-seccion-flexible">
                    <label class = "enlace-barra-izquierda">
                        <input type="checkbox" name="productos[]" value="{$idProd}" data-precio="{$prod->getPrecioFinal()}" onchange="calcularPrecios()" $checked>
                        {$prod->getNombre()}
                        <span class = "rol-usuario"> ({$precioIva}€)</span>
                    </label>

                    <div class = "form-cantidad">
                        <label class = "texto-ayuda">Cant:</label>
                        <input type="number" name="cantidad_{$idProd}" value="{$cantidad}" min="1" oninput="calcularPrecios()">
                    </div>
                </div>   
EOF;
        }

        return <<<EOF
        <div class="tarjeta-formulario formulario-estandar">
            <p>
                <label><strong>Nombre de la Oferta:</strong></label><br>
                <input type="text" name="nombre" value="$nombre" required class="input-formulario">
            </p>
            
            <p>
                <label><strong>Descripción:</strong></label><br>
                <textarea name="descripcion" rows="3" class="input-formulario">$descripcion</textarea>
            </p>

            <div>
                <p>
                    <label><strong>Fecha Inicio (AAAA-MM-DD):</strong></label><br>
                    <input type="text" name="comienzo" value="$comienzo" required class="input-formulario">
                </p>
                <p>
                    <label><strong>Fecha Fin (AAAA-MM-DD):</strong></label><br>
                    <input type="text" name="fin" value="$fin" required class="input-formulario">
                </p>
            </div>

            <h2 class="titulo-serif">🍔 Productos y Cantidades</h2>
            <div>
                $Productos
            </div>

            <p class="margen-superior"></p>

            <h2 class="titulo-serif">💰 Configuración de Precio</h2>
            
            <div id="info-precios">
                <span>Suma precios originales: <span id="precio_original_display">0.00</span>€</span>
            </div>

            <p>
                <label><strong>Precio Final Pack (€):</strong></label><br>
                <input type="number" id="precio_oferta" name="precio_oferta" class="input-formulario" oninput="calcularPrecios()">
            </p>

            <p>
                <label><strong>Porcentaje de Descuento (%):</strong></label><br>
                <input type="number" id="descuento" name="descuento" value="$descuento" class="input-formulario" readonly>
            </p>

           

            <div class="acciones-formulario">
                <button type="submit" class="btn-guardar">💾 GUARDAR OFERTA</button>
                <a href="gestion_ofertas.php" class="btn-cancelar">❌ CANCELAR</a>
            </div>
        </div>

        <script>
            function calcularPrecios() {
                let precioOriginalTotal = 0;
  
                const productosMarcados = document.querySelectorAll('input[name="productos[]"]:checked');
                
                productosMarcados.forEach(casilla => {
                    const idProducto = casilla.value;
  
                    const precioUnitario = parseFloat(casilla.getAttribute('data-precio'));
                    
                    const entradaCantidad = document.querySelector('input[name="cantidad_' + idProducto + '"]');
                    const cantidad = parseInt(entradaCantidad.value) || 1;
                    
                    precioOriginalTotal += (precioUnitario * cantidad);
                });


                const entradaPrecioOferta = document.getElementById('precio_oferta');
                const precioOferta = parseFloat(entradaPrecioOferta.value);
                
                const visorPrecioOriginal = document.getElementById('precio_original_display');
                const entradaDescuento = document.getElementById('descuento');
                const divInformacion = document.getElementById('info-precios');

                visorPrecioOriginal.innerText = precioOriginalTotal.toFixed(2);

                if (precioOriginalTotal > 0) {
                    divInformacion.style.display = 'inline-flex'; // Mostramos el cuadro de info
                    
                    if (precioOferta > 0) {
                        let ahorroDinero = precioOriginalTotal - precioOferta;
                        let porcentajeCalculado = (ahorroDinero / precioOriginalTotal) * 100;
                        
                        entradaDescuento.value = porcentajeCalculado > 0 ? porcentajeCalculado.toFixed(1) : 0;
                    }
                } else {
                    divInformacion.style.display = 'none';
                    entradaDescuento.value = 0;
                }
            }

            window.onload = calcularPrecios;
        </script>
EOF;
    }
    protected function procesaFormulario($datos) {
        $datos['id'] = $this->idOferta;

        if (empty($datos['nombre']) || empty($datos['comienzo']) || empty($datos['fin'])) {
            return ["Nombre y fechas (AAAA-MM-DD) son obligatorios."];
        }

        if (empty($datos['productos'])) {
            return ["Debes seleccionar al menos un producto para el pack."];
        }

        if (Oferta::guardaOActualiza($datos)) {
            header('Location: gestion_ofertas.php?msg=ok');
            exit();
        } else {
            return ["Error crítico: No se pudo guardar la oferta en la base de datos."];
        }
    }
}