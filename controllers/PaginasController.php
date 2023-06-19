<?php

namespace Controllers;

use Intervention\Image\Point;
use Model\Dia;
use Model\Hora;
use MVC\Router;
use Model\Evento;
use Model\Ponente;
use Model\Categoria;

class PaginasController {
    public static function index(Router $router) {

        $eventos = Evento::ordenar('hora_id', 'ASC');

        $eventos_formateados = []; // se va a ir llenando por este foreach
        foreach($eventos as $evento) {
            $evento->categoria = Categoria::find($evento->categoria_id);
            $evento->dia = Dia::find($evento->dia_id);
            $evento->hora = Hora::find($evento->hora_id);
            $evento->ponente = Ponente::find($evento->ponente_id);

            if($evento->dia_id === '1' && $evento->categoria_id === '1') { // viernes y conferencias
                $eventos_formateados['conferencias_v'][] = $evento; // en conferencias_v se guardaran cuando se cumplan las dos variables
            }
            if($evento->dia_id === '2' && $evento->categoria_id === '1') { // sabado y conferencias
                $eventos_formateados['conferencias_s'][] = $evento; // en conferencias_v se guardaran cuando se cumplan las dos variables
            }
            if($evento->dia_id === '1' && $evento->categoria_id === '2') { // viernes y conferencias
                $eventos_formateados['workshops_v'][] = $evento; // en workshops_v se guardaran cuando se cumplan las dos variables
            }
            if($evento->dia_id === '2' && $evento->categoria_id === '2') { // viernes y workshops
                $eventos_formateados['workshops_s'][] = $evento; // en workshops_v se guardaran cuando se cumplan las dos variables
            }

        }

        // Obtener el total de cada bloque
        $ponentes_total = Ponente::total();
        $conferencias_total = Evento::total('categoria_id', 1);
        $workshops_total = Evento::total('categoria_id', 2);

        // Obtener todos los ponentes
        $ponentes = Ponente::all();

        $router->render('paginas/index',[
            'titulo' => 'Inicio',
            'eventos' => $eventos_formateados,
            'ponentes_total' => $ponentes_total,
            'conferencias_total' => $conferencias_total,
            'workshops_total' => $workshops_total,
            'ponentes' => $ponentes
        ]);
    }

    public static function evento(Router $router) {

        $router->render('paginas/devwebcamp',[
            'titulo' => 'Sobre DevWebCamp'
        ]);
    }

    public static function paquetes(Router $router) {

        $router->render('paginas/paquetes',[
            'titulo' => 'Paquetes DevWebCamp'
        ]);
    }

    public static function conferencia(Router $router) {

        $eventos = Evento::ordenar('hora_id', 'ASC');

        $eventos_formateados = []; // se va a ir llenando por este foreach
        foreach($eventos as $evento) {
            $evento->categoria = Categoria::find($evento->categoria_id);
            $evento->dia = Dia::find($evento->dia_id);
            $evento->hora = Hora::find($evento->hora_id);
            $evento->ponente = Ponente::find($evento->ponente_id);

            if($evento->dia_id === '1' && $evento->categoria_id === '1') { // viernes y conferencias
                $eventos_formateados['conferencias_v'][] = $evento; // en conferencias_v se guardaran cuando se cumplan las dos variables
            }
            if($evento->dia_id === '2' && $evento->categoria_id === '1') { // sabado y conferencias
                $eventos_formateados['conferencias_s'][] = $evento; // en conferencias_v se guardaran cuando se cumplan las dos variables
            }
            if($evento->dia_id === '1' && $evento->categoria_id === '2') { // viernes y conferencias
                $eventos_formateados['workshops_v'][] = $evento; // en workshops_v se guardaran cuando se cumplan las dos variables
            }
            if($evento->dia_id === '2' && $evento->categoria_id === '2') { // viernes y workshops
                $eventos_formateados['workshops_s'][] = $evento; // en workshops_v se guardaran cuando se cumplan las dos variables
            }

        }


        $router->render('paginas/conferencias',[
            'titulo' => 'WorkShops & Conferencias',
            'eventos' => $eventos_formateados
        ]);
    }

    public static function error(Router $router) {
        
        $router->render('paginas/error',[
            'titulo' => 'Pagina no Encontrada'
        ]);
    }
}