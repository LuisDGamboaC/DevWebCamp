<?php 

namespace Model;

class Paquetes extends ActiveRecord {

    protected static $tabla = 'paquetes';
    protected static $columnasDB = ['id', 'nombre'];

    public $id;
    public $nombre;

}