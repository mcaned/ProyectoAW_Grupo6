<?php
// includes/Formulario.php

abstract class Formulario {
    protected $idForm;
    protected $action;

    public function __construct($idForm, $opciones = []) {
        $this->idForm = $idForm;
        $this->action = $opciones['action'] ?? $_SERVER['PHP_SELF'];
    }

    public function gestiona() {
        if (!$this->formularioEnviado($_POST)) {
            return $this->generaFormulario();
        } else {
            $resultado = $this->procesaFormulario($_POST);
            if (is_array($resultado)) { 
                return $this->generaFormulario($_POST, $resultado);
            }
            return $resultado; 
        }
    }

    private function formularioEnviado(&$params) {
        return isset($params['idFormulario']) && $params['idFormulario'] == $this->idForm;
    }

    private function generaFormulario($datos = [], $errores = []) {
        $html = "";
        if ($errores) {
            $html .= '<ul style="color:red;">';
            foreach ($errores as $error) $html .= "<li>$error</li>";
            $html .= '</ul>';
        }
        
        $campos = $this->generaCamposFormulario($datos);
        $html .= "<form method='POST' action='{$this->action}' id='{$this->idForm}'>";
        $html .= "<input type='hidden' name='idFormulario' value='{$this->idForm}' />";
        $html .= $campos;
        $html .= "</form>";
        return $html;
    }

    abstract protected function generaCamposFormulario($datosIniciales);
    abstract protected function procesaFormulario($datos);
}