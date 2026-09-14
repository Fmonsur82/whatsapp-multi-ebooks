<?php  
include '../models/mdl_EliminarCuenta.php';
$mdl_EliminarCuenta = new EliminarCuenta();

date_default_timezone_set('America/Bogota');
$fecha_hora = date('Y-m-d G:i:s');

$indicativo  = isset($_POST['indicativo'])? limpiarCadena($_POST['indicativo']) : "" ;
$telefono  = isset($_POST['telefono'])? limpiarCadena($_POST['telefono']) : "" ;

switch ($_GET['op']) {
	case 'listarPaises':
		$respuesta = $mdl_EliminarCuenta->listarPaises();
		while ($row = $respuesta->fetch_object()) {
			echo '<option value='.$row->indicativo_wsp.'>+'.$row->indicativo_wsp." / ".$row->nombre.'</option>';
		}
		break;
	case 'eliminarNumero':
		$respuesta = $mdl_EliminarCuenta->eliminarNumero($indicativo.$telefono);
		if ($respuesta) {
			$mdl_EliminarCuenta->mensajeCuentaEliminada($indicativo.$telefono);
			$mdl_EliminarCuenta->eliminarConversacion($indicativo.$telefono);
			$mensaje_respuesta = array(
				"icon"=>'success',
				"title"=>'!Número eliminado',
				"text"=>'Tu número se ha eliminado correctamente'

			);
		}else{
			$mensaje_respuesta = array(
				"icon"=>'error',
				"title"=>'!Error',
				"text"=>'No ha sido posible eliminar el numero, contacta con soporte',

			);
		}
		echo json_encode($mensaje_respuesta);
		break;
	default:
		echo json_encode(["status"=>"ERROR","msj"=>"No se encontro método"]);
		break;
}


?>