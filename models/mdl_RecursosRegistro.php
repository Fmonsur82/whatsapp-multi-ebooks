<?php 
include_once '../config/conexion.php';

class RecursosRegistro{

    function __construct(){}

    public function listarPaises(){
        $sql = "SELECT * FROM pais";
        return ejecutarConsulta($sql);
    }

    public function obtenerClientePorID($id_cliente){
        $sql = "SELECT * FROM cliente WHERE id_cliente = '$id_cliente'";
        return ejecutarConsultaSimpleFila($sql);
    }
}   

?>