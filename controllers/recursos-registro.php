<?php  
include '../models/mdl_RecursosRegistro.php';
$mdl_RecursosRegistro = new RecursosRegistro();

date_default_timezone_set('America/Bogota');
$fecha_hora = date('Y-m-d G:i:s');

$id_cliente  = isset($_POST['id_cliente'])? limpiarCadena($_POST['id_cliente']) : "" ;

switch ($_GET['op']) {
	case 'obtenerData':
		$data = array();
		$cliente = $mdl_RecursosRegistro->obtenerClientePorID($id_cliente);
		$obj_pais = $mdl_RecursosRegistro->listarPaises();
		$listaPais = array();
		foreach ($obj_pais as $key => $value) {
			array_push($listaPais, $value);
		}
		if (isset($_POST['id_cliente']) && !empty($_POST['id_cliente']))  {
            $id_cliente = $_POST['id_cliente'];
        }else{
            echo json_encode(['status'=>'ERROR','msl'=>'No se encontro id_cliente']);
        }
		$token_cliente = base64_encode(json_encode(["id_cliente"=>$cliente['id_cliente']]));
        $base_url = 'https://wsp-multi.dcsing.com/view/registro/?token='.$token_cliente;
        
		$data['listaPais'] = $listaPais;
		$data['cliente'] = $cliente;
		$data['url_registro'] = $base_url;

		echo json_encode($data);
		break;
	default:
		echo json_encode(["status"=>"ERROR","msj"=>"No se encontro método"]);
		break;
}


?>