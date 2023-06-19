<?php 

namespace Classes; 

class Paginacion {
    public $pagina_actual;
    public $registro_por_pagina;
    public $total_registros;

    public function __construct($pagina_actual = 1, $registro_por_pagina = 10, $total_registros = 0) 
    {
        $this->pagina_actual = (int) $pagina_actual; //castiar un valor (int) cambia los valores como "1" a 1
        $this->registro_por_pagina = (int) $registro_por_pagina;
        $this->total_registros = (int) $total_registros;
    }

    public function offset() { // en la pg 1 solo se muestras los reistros del 1-10 pg 2 11-20
        return $this->registro_por_pagina * ($this->pagina_actual - 1);
    }

    public function total_paginas() {
        return ceil($this->total_registros / $this->registro_por_pagina); // redondea los numeros
    }

    public function pagina_anterior() {
        $anterior = $this->pagina_actual - 1;
        return ($anterior > 0) ? $anterior : false; // si la pagina anterior es mayor a 0 se queda en la anterior del caso contrario retorna un false NUNCA VA LA PAGIAN 0 O -1
    }

    public function pagina_siguiente() {
        $siguiente = $this->pagina_actual + 1;
        return ($siguiente <= $this->total_paginas()) ? $siguiente : false; // no se pasa del total de paginas si solo hay 2 no se pasa el 3
        return $siguiente;
    }

    public function enlace_anterior() { //HTML
        $html = '';
        if($this->pagina_anterior()) {
            $html .= "<a class=\"paginacion__enlace paginacion__enlace--texto\" 
            href=\"?page={$this->pagina_anterior()}\">
            &laquo; Anterior</a>";
        }
        return $html;
    }

    public function enlace_siguiente() {
        $html = '';
        if($this->pagina_siguiente()) {
            $html .= "<a class=\"paginacion__enlace paginacion__enlace--texto\" 
            href=\"?page={$this->pagina_siguiente()}\">
            Siguiente &raquo;</a>";
        }
        return $html;
    }

    public function numeros_paginas() {
        $html = '';
        for($i = 1; $i <= $this->total_paginas(); $i++) {
            if($i === $this->pagina_actual) {
                $html .= "<span class=\" paginacion__enlace paginacion__enlace--actual \">{$i}</span>";
            }else {
                $html .= "<a class=\"paginacion__enlace paginacion__enlace--numero\" href=\"?page={$i} \">{$i}</a>";
            }
        }

        return $html;
    }

    public function paginacion() {
        $html = '';
        if($this->total_registros > 1) {
            $html .= '<div class="paginacion">';
            $html .= $this->enlace_anterior();
            $html .= $this->numeros_paginas();
            $html .= $this->enlace_siguiente();
            $html .= '</div>';

        }
        return $html;
    }
}