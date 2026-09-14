<?php 
include_once '../config/conexion.php';

class Services{

    function __construct(){}

    public function validarClientePorID($id_cliente){
        $sql = "SELECT * FROM cliente WHERE id_cliente = '$id_cliente'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function nuevoCliente($id_cliente,$nombre,$id_pais,$url_acceso,$logo_ruta){
        $sql = "INSERT INTO cliente(id_cliente,nombre,id_pais,url_acceso,logo_ruta)VALUES('$id_cliente','$nombre','$id_pais','$url_acceso','$logo_ruta')";
        return ejecutarConsulta($sql);
    }

    public function listarClientes(){
        $sql = "SELECT * FROM cliente ";
        return ejecutarConsulta($sql);
    }

    public function obtenerClientePorID($id_cliente){
        $sql = "SELECT * FROM cliente WHERE id_cliente = '$id_cliente'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function eliminarClientePorID($id_cliente){
        $sql = "DELETE FROM cliente WHERE id_cliente = '$id_cliente'";
        return ejecutarConsulta($sql);
    }

    public function obtenerPaisPorIndicativo($indicativo_wsp){
        $sql = "SELECT * FROM pais WHERE indicativo_wsp = '$indicativo_wsp'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function nuevoPais($nombre, $indicativo_lbl, $indicativo_wsp){
        $sql = "INSERT INTO pais(nombre, indicativo_lbl, indicativo_wsp) VALUES ('$nombre','$indicativo_lbl','$indicativo_wsp')";
        return ejecutarConsulta_retornaID($sql);
    }

    public function listarPaises(){
        $sql = "SELECT * FROM pais";
        return ejecutarConsulta($sql);
    }

    public function validarTokenBearer($headers){
        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Falta el token de autorización']);
            exit;
        }

        $authHeader = $headers['Authorization'];
        if (strpos($authHeader, 'Bearer ') !== 0) {
            http_response_code(401);
            echo json_encode(['error' => 'Formato de token inválido']);
            exit;
        }

        $token = substr($authHeader, 7); 

        $tokenValido = "AISNU4-DYHY-T58F-151S54UYA";
        if ($token !== $tokenValido) {
            http_response_code(403);
            echo json_encode(['error' => 'Token inválido']);
            exit;
        }

        return true;
    }
}   

?>