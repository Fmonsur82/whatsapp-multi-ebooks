<?php  
include '../models/mdl_Registro.php';
$mdl_Registro = new Registro();

$origen = $_SERVER['HTTP_ORIGIN'] ?? '';

if (preg_match('/^https:\/\/([a-z0-9-]+\.)*microsite-ebooks724\.com$/', $origen)) {
    header("Access-Control-Allow-Origin: $origen");
} else {
    header("Access-Control-Allow-Origin: https://microsite-ebooks724.com"); 
}

header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Manejo de preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

date_default_timezone_set('America/Bogota');
$fecha_hora = date('Y-m-d G:i:s');

$id_cliente  = isset($_POST['id_cliente'])? sanitizar($_POST['id_cliente']) : "" ;
$indicativo  = isset($_POST['indicativo'])? limpiarCadena($_POST['indicativo']) : "" ;
$telefono  = isset($_POST['telefono'])? limpiarCadena($_POST['telefono']) : "" ;
$aceptaPolPriv = ($_POST['acepta_pol_priv'] ?? '') === '1';
$aceptaTyc = ($_POST['acepta_tyc'] ?? '') === '1';
$dispositivo = substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? 'No informado'), 0, 500);
$ip = filter_var($_SERVER['REMOTE_ADDR'] ?? '', FILTER_VALIDATE_IP) ?: '';

switch ($_GET['op']) {
	case 'obtenerData':
		$data = array();
		$cliente = $mdl_Registro->obtenerClientePorID($id_cliente);
		$obj_pais = $mdl_Registro->listarPaises();
		$listaPais = array();
		foreach ($obj_pais as $key => $value) {
			array_push($listaPais, $value);
		}
		$data['listaPais'] = $listaPais;
		$data['cliente'] = $cliente;
		echo json_encode($data);
		break;
	case 'nuevoNumero':
		if (!$aceptaPolPriv || !$aceptaTyc) {
			echo json_encode([
				"icon"=>'warning',
				"title"=>'Aceptación requerida',
				"text"=>'Debes aceptar la política de tratamiento de datos y los términos y condiciones.',
			]);
			exit();
		}
		$validarUsuario = $mdl_Registro->obtenerUsuario($indicativo.$telefono);
		if ($validarUsuario) {
			$mensaje_respuesta = array(
				"icon"=>'info',
				"title"=>'!Tu número ya esta registrado',
				"text"=>'Ya puedes consultar la biblioteca digital',
			);
			echo json_encode($mensaje_respuesta);
			exit();
		}
		$respuesta = $mdl_Registro->nuevoNumero($indicativo.$telefono, $id_cliente, $dispositivo, $ip);
		if ($respuesta) {
			
			$mdl_Registro->mensajeBienvenida($indicativo.$telefono);
			$mensaje_respuesta = array(
				"icon"=>'success',
				"title"=>'!Número registrado',
				"text"=>'Tu número se ha registrado correctamente',
			);
			echo json_encode($mensaje_respuesta);
		}else{
			$mensaje_respuesta = array(
				"icon"=>'error',
				"title"=>'!Error',
				"text"=>'No ha sido registrar el numero',

			);
			echo json_encode($mensaje_respuesta);
		}
		break;
	default:
		echo json_encode(["status"=>"ERROR","msj"=>"No se encontro método"]);
		break;
}


function sanitizar($input) {
    // Elimina espacios extras al inicio y al final
    $input = trim($input);

    // Quita caracteres no visibles (tab, newline, etc.)
    $input = preg_replace('/\s+/', ' ', $input);

    // Convierte caracteres especiales en entidades HTML (previene XSS)
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');

    // Si es necesario, se puede usar addslashes (solo como capa extra)
    $input = addslashes($input);

    return $input;
}

?>
