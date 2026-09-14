<?php 
include_once '../config/conexion.php';

class Registro{

    function __construct(){}

    public function listarPaises(){
        $sql = "SELECT * FROM pais";
        return ejecutarConsulta($sql);
    }

    public function obtenerClientePorID($id_cliente){
        $sql = "SELECT * FROM cliente WHERE id_cliente = '$id_cliente'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function nuevoNumero($telefono, $id_cliente){
        $sql = "INSERT INTO usuarios_multi_cliente(telefono,id_cliente)VALUES('$telefono','$id_cliente')";
        return ejecutarConsulta($sql);
    }

    public function obtenerUsuario($telefono){
        $sql = "SELECT * FROM usuarios_multi_cliente WHERE telefono = '$telefono'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function mensajeBienvenida($telefono){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://wsp-multi.dcsing.com/webhook.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
                "object": "bienvenida",
                "telefono": '.$telefono.'}',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
    }
}   

?>