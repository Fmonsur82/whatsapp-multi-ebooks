<?php  
include '../models/mdl_Services.php';
$mdl_Services = new Services();

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

date_default_timezone_set('America/Bogota');
$fecha_hora = date('Y-m-d G:i:s');


switch ($_GET['method']) {
    case 'nuevoCliente':
        if (isset($_POST['id_cliente']) && !empty($_POST['id_cliente'])) {
            $id_cliente = $_POST['id_cliente'];
        } else {
            echo json_encode(['status' => 'ERROR', 'msj' => 'No se encontró id_cliente']);
            exit();
        }

        if (isset($_POST['nombre']) && !empty($_POST['nombre'])) {
            $nombre = $_POST['nombre'];
        } else {
            echo json_encode(['status' => 'ERROR', 'msj' => 'No se encontró nombre']);
            exit();
        }

        if (isset($_POST['id_pais']) && !empty($_POST['id_pais'])) {
            $id_pais = $_POST['id_pais'];
        } else {
            echo json_encode(['status' => 'ERROR', 'msj' => 'No se encontró identificador del país']);
            exit();
        }

        if (isset($_POST['url_acceso']) && !empty($_POST['url_acceso'])) {
            $url_acceso = $_POST['url_acceso'];
        } else {
            echo json_encode(['status' => 'ERROR', 'msj' => 'No se encontró url_acceso']);
            exit();
        }

        $cliente = $mdl_Services->validarClientePorID($id_cliente);
        if ($cliente) {
            echo json_encode(['status' => 'ERROR', 'msj' => 'El cliente ya se encuentra registrado']);
            exit();
        }

        $logo_ruta = null;
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $directorio_destino = '../public/img/logos/';

            if (!is_dir($directorio_destino)) {
                mkdir($directorio_destino, 0755, true);
            }

            $nombre_archivo = basename($_FILES['logo']['name']);
            $extension = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));

            $extensiones_permitidas = ['jpg', 'jpeg', 'png'];
            if (!in_array($extension, $extensiones_permitidas)) {
                echo json_encode(['status' => 'ERROR', 'msj' => 'El archivo debe ser una imagen (jpg, jpeg, png, gif, webp)']);
                exit();
            }

            $tipo_mime = mime_content_type($_FILES['logo']['tmp_name']);
            if (strpos($tipo_mime, 'image/') !== 0) {
                echo json_encode(['status' => 'ERROR', 'msj' => 'El archivo no es una imagen válida']);
                exit();
            }

            $nombre_final = 'logo_' . $id_cliente . '.' . $extension;
            $ruta_final = $directorio_destino . $nombre_final;

            if (move_uploaded_file($_FILES['logo']['tmp_name'], $ruta_final)) {
                $logo_ruta = 'public/img/logos/' . $nombre_final;
            } else {
                echo json_encode(['status' => 'ERROR', 'msj' => 'No se pudo subir el logo']);
                exit();
            }
        }

        $nuevo_cliente = $mdl_Services->nuevoCliente($id_cliente, $nombre, $id_pais, $url_acceso, $logo_ruta);

        if ($nuevo_cliente) {
            echo json_encode(['status' => 'SUCCESS', 'msj' => 'El cliente se creó correctamente']);
        } else {
            echo json_encode(['status' => 'ERROR', 'msj' => 'Hubo un error al registrar el cliente']);
        }
        break;
    case 'listarClientes':
        $obj_cliente = $mdl_Services->listarClientes();
        foreach ($obj_cliente as $key => $value) {
            echo json_encode($value);
        }
        break;
    case 'obtenerClientePorID':
        if (isset($_POST['id_cliente']) && !empty($_POST['id_cliente']))  {
            $id_cliente = $_POST['id_cliente'];
        }else{
            echo json_encode(['status'=>'ERROR','msj'=>'No se encontro id_cliente']);
        }

        $cliente = $mdl_Services->obtenerClientePorID($id_cliente);
        $token_cliente = base64_encode(json_encode(["id_cliente"=>$cliente['id_cliente']]));

        $url_qr = 'https://wsp-multi.dcsing.com/view/recursos-registro/?token='.$token_cliente;
        $url_registro =  $base_url = 'https://wsp-multi.dcsing.com/view/registro/?token='.$token_cliente;

        $cliente['url_qr'] = $url_qr;
        $cliente['url_registro'] = $url_registro;

        echo json_encode($cliente);

        break;
    case 'eliminarClientePorID':
        if (isset($_POST['id_cliente']) && !empty($_POST['id_cliente'])) {
            $id_cliente = $_POST['id_cliente'];
        } else {
            echo json_encode(['status' => 'ERROR', 'msj' => 'No se encontró id_cliente']);
            exit();
        }

        $extensiones = ['jpg', 'jpeg', 'png'];
        foreach ($extensiones as $ext) {
            $ruta_logo = "../public/img/logos/logo_{$id_cliente}." . $ext;
            if (file_exists($ruta_logo)) {
                unlink($ruta_logo);
                break; 
            }
        }

        $delete = $mdl_Services->eliminarClientePorID($id_cliente);
        if ($delete) {
            echo json_encode(['status' => 'SUCCESS', 'msj' => 'Cliente eliminado correctamente']);
        } else {
            echo json_encode(['status' => 'ERROR', 'msj' => 'No fue posible eliminar cliente']);
        }
        break;

    case 'obtenerUrlDeRegistro':
        if (isset($_POST['id_cliente']) && !empty($_POST['id_cliente']))  {
            $id_cliente = $_POST['id_cliente'];
        }else{
            echo json_encode(['status'=>'ERROR','msj'=>'No se encontro id_cliente']);
        }
        $cliente = $mdl_Services->obtenerClientePorID($id_cliente);
        if ($cliente) {
            $token_cliente = base64_encode(json_encode(["id_cliente"=>$cliente['id_cliente']]));
            $base_url = 'https://wsp-multi.dcsing.com/view/registro/?token='.$token_cliente;
            echo json_encode(["url"=>$base_url]);
        }else{
            echo json_encode(['status'=>'ERROR','msj'=>'El cliente no existe']);
        }
        break;
    case 'obtenerUrlDerecursos':
        if (isset($_POST['id_cliente']) && !empty($_POST['id_cliente']))  {
            $id_cliente = $_POST['id_cliente'];
        }else{
            echo json_encode(['status'=>'ERROR','msj'=>'No se encontro id_cliente']);
        }
        $cliente = $mdl_Services->obtenerClientePorID($id_cliente);
        if ($cliente) {
            $token_cliente = base64_encode(json_encode(["id_cliente"=>$cliente['id_cliente']]));
            $base_url = 'https://wsp-multi.dcsing.com/view/recursos-registro/?token='.$token_cliente;
            echo json_encode(["url"=>$base_url]);
        }else{
            echo json_encode(['status'=>'ERROR','msj'=>'El cliente no existe']);
        }
        break;
    case 'nuevoPais':
        if (isset($_POST['nombre']) && !empty($_POST['nombre'])) {
            $nombre = $_POST['nombre'];
        } else {
            echo json_encode(['status' => 'ERROR', 'msj' => 'No se encontró nombre']);
            exit();
        }

        if (isset($_POST['indicativo_lbl']) && !empty($_POST['indicativo_lbl'])) {
            $indicativo_lbl = $_POST['indicativo_lbl'];
        } else {
            echo json_encode(['status' => 'ERROR', 'msj' => 'No se encontró indicativo_lbl']);
            exit();
        }

        if (isset($_POST['indicativo_wsp']) && !empty($_POST['indicativo_wsp'])) {
            $indicativo_wsp = $_POST['indicativo_wsp'];
        } else {
            echo json_encode(['status' => 'ERROR', 'msj' => 'No se encontró indicativo_wsp']);
            exit();
        }

        $existe = $mdl_Services->obtenerPaisPorIndicativo($indicativo_wsp);
        if ($existe) {
            echo json_encode(['status'=>'ERROR',
                'msj'=>"Este indicativo ya se encuentra registrado",
                "id" =>$existe['id'],
                "nombre" =>$existe['nombre'],
                "indicativo_lbl" =>$existe['indicativo_lbl'],
                "indicativo_wsp" =>$existe['indicativo_wsp']
            ]);
            exit();
        }

        $respuesta = $mdl_Services->nuevoPais($nombre, $indicativo_lbl, $indicativo_wsp);
        if ($respuesta) {
            echo json_encode(["status"=>"OK","id_cliente" => $respuesta]);
        }else{
            echo json_encode(['status'=>'ERROR','msj'=>'No fue posible registrar el país']);
        }
    
        break;
    case 'listarPaises':
        $obj_pais = $mdl_Services->listarPaises();
        $pais = array();
        foreach ($obj_pais as $key => $value) {
            array_push($pais, $value);
        }
        echo json_encode($pais);
        break;
    case 'estadoWhatsApp':
        $headers = apache_request_headers();
        $a = $mdl_Services->validarTokenBearer($headers);
        if ($a) {
            if (isset($_POST['estadoWhatsApp']) && !empty($_POST['estadoWhatsApp'])) {
                $estadoWhatsApp = $_POST['estadoWhatsApp'];
            } else {
                echo json_encode(['status' => 'ERROR', 'msj' => 'No se encontró estadoWhatsApp']);
                exit();
            }
        }   
        if ($estadoWhatsApp != '3' || $estadoWhatsApp != '4') {
            echo json_encode(["status"=>"ERROR","msj"=>"El estado invalido"]);
            exit();
        }
            
        echo json_encode(['estadoWhatsApp'=>$estadoWhatsApp]);
        break;
    default:
        echo json_encode(["status"=>"ERROR","msj"=>"El método no existe"]);
        break;
}






?>