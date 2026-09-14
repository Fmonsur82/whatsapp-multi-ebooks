<?php 
include_once '../config/conexion.php';

class EliminarCuenta{

    function __construct(){}

    public function listarPaises(){
        $sql = "SELECT * FROM pais";
        return ejecutarConsulta($sql);
    }

    // public function eliminarNumero($telefono){
    //     $sql = "UPDATE usuarios_multi_cliente 
    //     SET telefono = CONCAT('DELETE', telefono)
    //     WHERE telefono = '$telefono'";

    //     return ejecutarConsulta($sql);
    // }

    public function eliminarNumero($telefono) {
        $sql = "UPDATE usuarios_multi_cliente 
        SET telefono = CONCAT('DELETE', telefono)
        WHERE telefono = '$telefono'";

        $resultado = ejecutarConsulta($sql);

        $cacheFile = __DIR__ . '/../cache/user_' . $telefono . '.json';
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }

        return $resultado;
    }

    public function eliminarConversacion($telefono){
        $sql = "UPDATE conversacion 
        SET no_celular = CONCAT('DELETE', no_celular)
        WHERE no_celular = '$telefono'";
        return ejecutarConsulta_retornaID($sql);
    }
    
    public function eliminarConversacionMensajes($telefono){
        $sql = "UPDATE conversacion_mensajes 
        SET no_celular = CONCAT('DELETE', no_celular)
        WHERE no_celular = '$telefono'";
        return ejecutarConsulta($sql);
    }

    public function mensajeCuentaEliminada($telefono){
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
                "object": "cuenta_eliminada",
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