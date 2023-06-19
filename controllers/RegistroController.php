<?php

namespace Controllers;

use Model\Dia;
use Model\Hora;
use MVC\Router;
use Model\Evento;
use Model\Ponente;
use Model\Usuario;
use Model\Paquetes;
use Model\Registro;
use Model\Categoria;
use Model\EventosRegistros;
use Model\Regalo;

class RegistroController {

    public static function crear(Router $router) {

        if(!is_auth()) {
            header('Location: /');
            return;
        }

        // Verificar si el usuario ya eligio un plan
        $registro = Registro::where('usuario_id', $_SESSION['id']);

        if(isset($registro) && ($registro->paquete_id === "3" || $registro->paquete_id === "2")) {
            header('Location: /boleto?id=' . urlencode($registro->token)); // urlencode se usa para evitar caracteres espceciales
            return;
        }
        if(isset($registro) && $registro->paquete_id === "1") {
            header('Location: /finalizar-registro/conferencias');
            return;
        }

        $router->render('registro/crear', [
            'titulo' => 'Finalizar Registro'
        ]);
    }

    public static function gratis(Router $router) {

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
           if(!is_auth()){
                header('Location: /login');
            return;
            }
                    // Verificar si el usuario ya eligio un plan
        $registro = Registro::where('usuario_id', $_SESSION['id']);

        if(isset($registro) && $registro->paquete_id === "3") {
            header('Location: /boleto?id=' . urlencode($registro->token)); // urlencode se usa para evitar caracteres espceciales
            return;
        }
            $token = substr( md5( uniqid(rand(), true)), 0, 8);// un token de 0 caracteres
            
            // Crear registro
            $datos = [
                'paquete_id' => 3,
                'pago_id' => '',
                'token' => $token,
                'usuario_id' => $_SESSION['id']
            ];

            $registro = new Registro($datos);
            $resultado = $registro->guardar();

            if($resultado) {
                header('Location: /boleto?id=' . urlencode($registro->token)); // urlencode se usa para evitar caracteres espceciales
            return;
            }
        }
    }

    public static function boleto(Router $router) {

        // Validar la URL
        $id = $_GET['id'];

        if(!$id || !strlen($id) === 8) {
            header('Location: /');
            return;
        }

        // Buscar en la DB
        $registro = Registro::where('token', $id);
        if(!$registro) {
            header('Location: /');
            return;
        }

        // Llenar las tablas de referencia
        $registro->usuario = Usuario::find($registro->usuario_id);
        $registro->paquete = Paquetes::find($registro->paquete_id);

        $router->render('registro/boleto', [
            'titulo' => 'Asistencia a DevWebCamp',
            'registro' => $registro
        ]);
    }

    public static function pagar(Router $router) {

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
           if(!is_auth()){
                header('Location: /login');
            return;
            }
            // Validar que POST  no venga vacio
            if(empty($_POST)) {
                echo json_encode([]);
                return;
            }

            // Crear el registro
            $datos = $_POST;
            $datos['token'] = substr( md5( uniqid(rand(), true)), 0, 8);
            $datos['usuario_id'] = $_SESSION['id'];

            try {
                $registro = new Registro($datos);
                $resultado = $registro->guardar();
                echo json_encode($resultado);
            } catch (\Throwable $th) {
                echo json_encode([
                    'resultado' => 'error'
                ]);
            }
        }
    }

    public static function conferencias(Router $router) {

        if(!is_auth()){
            header('Location: /login');
            return;
        }

        // Validar que el pusuario tenga el plan presencial
        $usuario_id = $_SESSION['id'];
        $registro = Registro::where('usuario_id', $usuario_id);

        if(isset($registro) && $registro->paquete_id === "2"){
            header('Location: /boleto?id=' . urlencode($registro->token)); // urlencode se usa para evitar caracteres espceciales
            return;
        }

        if($registro->paquete_id !== "1") {
            header('Location: /');
            return;
        }
        // Redireccionar a boleto virtual en caso de haber finalizado su registro
        if(isset($registro->regalo_id) && $registro->paquete_id === "1") { // si existe regalo_id
            header('Location: /boleto?id=' . urlencode($registro->token)); // urlencode se usa para evitar caracteres espceciales
            return;
        }
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

        $regalos = Regalo::all('ASC');

        // Manejando el registro mediante $_POST
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Revisar que el usuario este autenticado
            if(!is_auth()){
                header('Location: /login');
            return;
            }

            $eventos = explode(',', $_POST['eventos']); // array(4) { [0]=>string(1) "3"[1]=>string(1) "6"}
            if(empty($eventos)) {
                echo json_encode(['resultado' => false]);
                return;
            }

            // Obtener el registro de usuario
            $registro = Registro::where('usuario_id', $_SESSION['id']); 
            if(!isset($registro)|| $registro->paquete_id !== "1") {// no encontro un usuario con ese registro
                echo json_encode(['resultado' => false]);
                return;
            }

            $eventos_array = [];
            //  Validar la disponiblidad de los eventos seleccionados
            foreach($eventos as $evento_id) {
                $evento = Evento::find($evento_id);
                // Comprobar que el evento exista
                if(!isset($evento) || $evento->disponibles === "0" ) { // El evento existe pero ya no hay disponibles
                    echo json_encode(['resultado' => false]); // retorna un error
                    return;
                }
                $eventos_array[] = $evento; // lo gurdamos en memoria // si llego hasta aca significa que si existe el registro evento
            }
            foreach($eventos_array as $evento) {
                $evento->disponibles -= 1;
                $evento->guardar();

                // Almacenar el registro
                $datos = [
                    'evento_id' => (int) $evento->id,
                    'registro_id' => (int) $registro->id
                ];

                $registro_usuario = new EventosRegistros($datos);
                $registro_usuario->guardar();
            }
            // Almacenar el regalo
            $registro->sincronizar(['regalo_id' => $_POST['regalo_id']]);
            $resultado = $registro->guardar();
            if($resultado) {
                echo json_encode([
                    'resultado' => $resultado,
                    'token' => $registro->token
                ]);
            } else {
                echo json_encode(['resultado' => false]);
            }
            return;
        }

        
        $router->render('registro/conferencias', [
            'titulo' => 'Elige Workshops y Conferncias',
            'eventos' => $eventos_formateados,
            'regalos' => $regalos
            
        ]);
    }
    
}