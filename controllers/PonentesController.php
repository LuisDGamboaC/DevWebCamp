<?php

namespace Controllers;

use Classes\Paginacion;
use MVC\Router;
use Model\Ponente;
use Intervention\Image\ImageManagerStatic as Image;

class PonentesController {

    public static function index(Router $router) {

        $pagina_actual = $_GET['page'];
        $pagina_actual = filter_var($pagina_actual, FILTER_VALIDATE_INT);

        if(!$pagina_actual || $pagina_actual < 1) {
            header('Location: /admin/ponentes?page=1');
        }
        $registro_por_pagina = 6;
        $total_registros = Ponente::total(); 
        $paginacion = new Paginacion($pagina_actual, $registro_por_pagina, $total_registros);

        if($paginacion->total_paginas() < $pagina_actual) {// Para que no se pase de mas de las paginas totales que tenemos en total ej: 18 registros y se muestran 10 por pg solo habran 2 pag no puede exister pg 100
            header('Location: /admin/ponentes?page=1');
        }

        $ponentes = Ponente::paginar($registro_por_pagina, $paginacion->offset());

        if(!is_admin()) {
            header('Location: /login');
        }
        $router->render('admin/ponentes/index', [
            'titulo' => 'Ponentes / Conferencia',
            'ponentes' => $ponentes,
            'paginacion' => $paginacion->paginacion()
        ]);
    }

    public static function crear(Router $router) {
        if(!is_admin()) {
            header('Location: /login');
        }

        $alertas = [];
        $ponente = New Ponente;

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            if(!is_admin()) {
                header('Location: /login');
            }

            // Leer Imagen
            if(!empty($_FILES['imagen']['tmp_name'])) {

                $carpetaImagenes = '../public/img/speakers';

                // Crear la carpeta si no existe
                if(!is_dir($carpetaImagenes)) {
                    mkdir($carpetaImagenes, 0777, true);
                }

                $imagen_png = Image::make($_FILES['imagen']['tmp_name'])->fit(800,800)->encode('png', 80);
                $imagen_webp = Image::make($_FILES['imagen']['tmp_name'])->fit(800,800)->encode('png', 80);

                $nombre_imagen = md5(uniqid(rand(), true)); // genera nombre aleatorio

                $_POST['imagen'] = $nombre_imagen;
            }

            $_POST['redes'] = json_encode(  $_POST['redes'], JSON_UNESCAPED_SLASHES); // manda las redes que estaban en un arreglo com un string porque daba error la sanitizacion

            $ponente->sincronizar($_POST);

            //validar
            $alertas = $ponente->validar();

            // Guardar el Regitro
            if(empty($alertas)) {

                // GUARDAR LAS IMAGENES
                $imagen_png->save($carpetaImagenes . '/' . $nombre_imagen . '.png'); // lo guarda en la base de datos con el nombre png
                $imagen_webp->save($carpetaImagenes . '/' . $nombre_imagen . '.webp'); // lo guarda en la base de datos con el nombre webp

                // Guardar en la base de datos
                $resultado = $ponente->guardar();

                if($resultado) {
                    header('Location: /admin/ponentes');
                }
            }
        }

        $router->render('admin/ponentes/crear', [
            'titulo' => 'Registrar Ponente',
            'alertas' => $alertas,
            'ponente' => $ponente,
            'redes' => json_decode($ponente->redes) // tomamos el string y lo convertimos a un objeto de json

        ]);
    }

    public static function editar(Router $router) {

        if(!is_admin()) {
            header('Location: /login');
        }

        $alertas = [];
        
        // Validar el Id
        $id = $_GET['id'];
        $id = filter_var($id, FILTER_VALIDATE_INT); // el id siempre tiene que ser un numero entero o si no no funcinoa

        if(!$id) {
            header('Location: /admin/ponentes');
        }

        // Obtener Ponente a editar
        $ponente = Ponente::find($id);

        if(!$ponente) {
            header('Location: /admin/ponentes');
        }

        $ponente->imagen_actual = $ponente->imagen;

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            if(!is_admin()) {
                header('Location: /login');
            }
            // Leer Imagen
            if(!empty($_FILES['imagen']['tmp_name'])) {

                $carpetaImagenes = '../public/img/speakers';

                // Crear la carpeta si no existe
                if(!is_dir($carpetaImagenes)) {
                    mkdir($carpetaImagenes, 0777, true);
                }

                $imagen_png = Image::make($_FILES['imagen']['tmp_name'])->fit(800,800)->encode('png', 80);
                $imagen_webp = Image::make($_FILES['imagen']['tmp_name'])->fit(800,800)->encode('png', 80);

                $nombre_imagen = md5(uniqid(rand(), true)); // genera nombre aleatorio

                $_POST['imagen'] = $nombre_imagen;
            } else { // si no hay imagen recuperamos la imagen actual
                $_POST['imagen'] = $ponente->imagen_actual;
            }

            $_POST['redes'] = json_encode(  $_POST['redes'], JSON_UNESCAPED_SLASHES); // manda las redes que estaban en un arreglo com un string porque daba error la sanitizacion
            $ponente->sincronizar($_POST);
            $alertas = $ponente->validar();

            if(empty($alertas)) {
                if(isset($nombre_imagen)) { // si existe una imagen nueva
                // GUARDAR LAS IMAGENES
                $imagen_png->save($carpetaImagenes . '/' . $nombre_imagen . '.png'); // lo guarda en la base de datos con el nombre png
                $imagen_webp->save($carpetaImagenes . '/' . $nombre_imagen . '.webp'); // lo guarda en la base de datos con el nombre webp
                }
                $resultado = $ponente->guardar();

                if($resultado) {
                    header('Location: /admin/ponentes');
                }
            }
        }


        $router->render('admin/ponentes/editar', [
            'titulo' => 'Actualizar Ponente',
            'alertas' => $alertas,
            'ponente' => $ponente,
            'redes' => json_decode($ponente->redes) // tomamos el string y lo convertimos a un objeto de json
        ]);
    }

    public static function eliminar() {

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            if(!is_admin()) {
                header('Location: /login');
            }

            $id = $_POST['id'];
            $ponente = Ponente::find($id);

            if(!isset($ponente)) {
                header('Location: /admin/ponentes');
            }

            $resultado = $ponente->eliminar();

            if($resultado) {
                header('Location: /admin/ponentes'); // se refersca la pantalla porque vuelve a la misma pagina
            }

            debuguear($ponente);
        }
    }
}