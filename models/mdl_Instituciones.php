<?php 
include_once '../config/conexion.php';

class Instituciones{

    function __construct(){}

    public function listarPaises(){
        $sql = "SELECT * FROM pais";
        return ejecutarConsulta($sql);
    }

    public function selectInstitucion($id_pais){
        $sql = "SELECT * FROM cliente WHERE id_pais = '$id_pais'";
        return ejecutarConsulta($sql);
    }
}   

?>