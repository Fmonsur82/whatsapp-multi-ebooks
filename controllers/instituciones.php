<?php  
include '../models/mdl_Instituciones.php';
$mdl_Instituciones = new Instituciones();

date_default_timezone_set('America/Bogota');
$fecha_hora = date('Y-m-d G:i:s');

$id_pais  = isset($_POST['id_pais'])? limpiarCadena($_POST['id_pais']) : "" ;
$id_cliente  = isset($_POST['id_cliente'])? limpiarCadena($_POST['id_cliente']) : "" ;

switch ($_GET['op']) {
	case 'listarPaises':
		$respuesta = $mdl_Instituciones->listarPaises();
		while ($row = $respuesta->fetch_object()) {
			echo '<option value='.$row->id.'>'.$row->nombre.'</option>';
		}
		break;
	case 'selectInstitucion':
		$respuesta = $mdl_Instituciones->selectInstitucion($id_pais);
		while ($row = $respuesta->fetch_object()) {
			echo '<option value='.$row->id_cliente.'>'.$row->nombre.'</option>';
		}
		break;
	case 'vistaRegistro':
		$token_cliente = base64_encode(json_encode(["id_cliente"=>$id_cliente]));
        $base_url = 'https://wsp-multi.dcsing.com/view/registro/?token='.$token_cliente;
        echo json_encode($base_url);
		break;
	default:
		echo json_encode(["status"=>"ERROR","msj"=>"No se encontro método"]);
		break;
}


?>