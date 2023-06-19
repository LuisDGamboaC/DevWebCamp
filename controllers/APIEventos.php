<?php 

namespace Controllers;

use Model\EventoHorario;

class APIEventos { // le vamos a pasar el JS {categoria_id: '', dia: ''}

    public static function index() {
        $dia_id = $_GET['dia_id'] ?? '';
        $categoria_id = $_GET['categoria_id'] ?? '';

        $dia_id = filter_var($dia_id, FILTER_VALIDATE_INT);
        $categoria_id = filter_var($categoria_id, FILTER_VALIDATE_INT);

        if(!$dia_id || !$categoria_id) {
            echo json_encode([]); // nos retorna un arreglo vacio
            return;
        }

        // Consultar base de datos
        $evento = EventoHorario::wherArray(['dia_id' => $dia_id, 'categoria_id' => $categoria_id]) ?? [];

        echo json_encode($evento);
    }
}