<?php

function debuguear($variable) : string {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}
function s($html) : string {
    $s = htmlspecialchars($html);
    return $s;
}

function pagina_actual($path) :bool { // hace que resalte el boton del sidebar dependiendo en que url estamos
    return str_contains($_SERVER['PATH_INFO'] ?? '/', $path ) ? true : false ;
}

// Revisar si esta autenticado 
function is_auth() :bool {
    if(!isset($_SESSION)) {
        session_start();
    }
    return isset($_SESSION['nombre']) && !empty($_SESSION);
}

// Administradores
function is_admin() :bool {
    if(!isset($_SESSION)) {
        session_start();
    }
    return isset($_SESSION['admin']) && !empty($_SESSION['admin']); // que no este vacio entrar a session y colocarle admin
} 

function aos_animacion() :void {
    $efectos = ['fade-up', 'fade-down-right', 'fade-up-left', 'fade-down', 'flip-left', 'flip-right', 'zoom-in-up', 'zoom-in-down', 'zoom-out-up', 'zoom-out-down'];

    $efecto = array_rand($efectos, 1);
    echo ' data-aos="' . ($efectos[$efecto]) . '" ';
    
}