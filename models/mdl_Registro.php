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

    public function nuevoNumero($telefono, $id_cliente, $dispositivo, $ip){
        $conexion = Conexion::getConexion();
        $stmt = $conexion->prepare('INSERT INTO usuarios_multi_cliente (telefono, id_cliente, acepta_pol_priv, acepta_tyc, acepta_fecha_hora, acepta_dispositivo, acepta_ip, acepta_pol_priv_version, acepta_tyc_version) VALUES (?, ?, 1, 1, NOW(), ?, ?, ?, ?)');
        if (!$stmt) {
            return false;
        }
        $politicaVersion = 'v3-202607';
        $tycVersion = 'v2-202609';
        $stmt->bind_param('sissss', $telefono, $id_cliente, $dispositivo, $ip, $politicaVersion, $tycVersion);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
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
