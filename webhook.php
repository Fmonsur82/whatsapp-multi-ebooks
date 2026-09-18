<?php  
const TOKEN_FM = "73h8wjs8wipeopjh77hdkjh8975hjdj";
const WEBHOOK_url = "https://wsp-multi.dcsing.com/webhook.php";

require 'models/mdl_webhook.php';
$mdl_webhook = new Webhook();

date_default_timezone_set('America/Bogota');
$fecha = date('Y-m-d');
$fecha_hora = date('Y-m-d G:i:s');

header('Content-Type: application/json');


// ====================================================
// VALIDACIÓN DEL REQUEST POST Y LOS DATOS DE USUARIO
// ====================================================
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
	http_response_code(405);
	echo json_encode(['status' => 'ERROR', 'msj' => 'Método no permitido']);
	exit();	
}
$input = file_get_contents('php://input');
$headersSolicitud = $mdl_webhook->obtenerHeadersSolicitud();
$firmaRecibida = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? null;
$firmaValida = $mdl_webhook->validarFirma($input, $firmaRecibida);
$entrada = null;
$resultadoLog = $firmaValida ? 'aceptado' : 'firma_invalida';
$httpStatusLog = $firmaValida ? 200 : 401;
$errorCodigoLog = $firmaValida ? null : 'firma_invalida';
$errorMensajeLog = $firmaValida ? null : 'Firma X-Hub-Signature-256 ausente, no configurada o invalida';

register_shutdown_function(function () use (&$entrada, &$resultadoLog, &$httpStatusLog, &$errorCodigoLog, &$errorMensajeLog, $firmaValida, $headersSolicitud, $input, $mdl_webhook) {
    $fatal = error_get_last();
    if ($fatal && in_array($fatal['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        $resultadoLog = 'error_interno';
        $httpStatusLog = 500;
        $errorCodigoLog = 'error_fatal';
        $errorMensajeLog = substr($fatal['message'], 0, 500);
    }

    $valor = is_array($entrada) ? ($entrada['entry'][0]['changes'][0]['value'] ?? []) : [];
    $metadata = is_array($valor['metadata'] ?? null) ? $valor['metadata'] : [];
    $entry = is_array($entrada['entry'][0] ?? null) ? $entrada['entry'][0] : [];
    $tipoEvento = isset($valor['messages']) ? 'messages' : (isset($valor['statuses']) ? 'statuses' : ($entrada['object'] ?? null));

    $mdl_webhook->registrarWebhookLog([
        'meta_waba_id' => $entry['id'] ?? ($metadata['waba_id'] ?? null),
        'meta_phone_number_id' => $metadata['phone_number_id'] ?? null,
        'tipo_evento' => is_string($tipoEvento) ? substr($tipoEvento, 0, 80) : null,
        'firma_valida' => $firmaValida,
        'resultado' => $resultadoLog,
        'http_status_respuesta' => http_response_code() ?: $httpStatusLog,
        'error_codigo' => $errorCodigoLog,
        'error_mensaje' => $errorMensajeLog,
        'payload' => $firmaValida ? $entrada : null,
        'headers' => $headersSolicitud
    ]);
});

if (!$firmaValida) {
    http_response_code(401);
    echo json_encode(['status' => 'ERROR', 'msj' => 'Firma invalida']);
    exit();
}

$entrada = json_decode($input,true);

if (!is_array($entrada)) {
	$resultadoLog = 'payload_invalido';
	$httpStatusLog = 400;
	$errorCodigoLog = 'json_invalido';
	$errorMensajeLog = json_last_error_msg();
	http_response_code(400);
	echo json_encode(['status' => 'ERROR', 'msj' => 'JSON inválido']);
	exit();
}

$mdl_webhook->logEnRaw($entrada);


$valorWhatsapp = $entrada['entry'][0]['changes'][0]['value'] ?? [];

// Los estados (sent, delivered, read, failed) son acuses de recibo de Meta.
// No son mensajes de usuario: procesarlos como tales abría una conexión MySQL
// inútil por cada estado y agotaba la cuota horaria del hosting.
if (!empty($valorWhatsapp['statuses']) && empty($valorWhatsapp['messages'])) {
	$resultadoLog = 'ignorado';
	foreach ($valorWhatsapp['statuses'] as $st) {
		$id = $st['id'] ?? '';
		$status = $st['status'] ?? '';
		$errors = $st['errors'] ?? [];
		$mdl_webhook->logEnRaw("WA STATUS id=$id status=$status errors=" . json_encode($errors));
	}

	http_response_code(200);
	echo json_encode(['status' => 'OK']);
	exit();
}

function normalizarEntradaWhatsapp(array $entrada): ?array {
    $valor = $entrada['entry'][0]['changes'][0]['value'] ?? null;
    if (!$valor) return null;

    $mensaje = $valor['messages'][0] ?? null;
    $status  = $valor['statuses'][0] ?? null;
    $contacto = $valor['contacts'][0] ?? [];

    // Meta puede omitir el teléfono cuando el usuario usa username. El BSUID
    // permite responder en ese caso mediante el campo API `recipient`.
    $telefono = $mensaje['from'] ?? ($contacto['wa_id'] ?? null);
    $bsuid = $mensaje['from_user_id'] ?? ($contacto['user_id'] ?? null);
    $destino = $telefono !== null && $telefono !== ''
        ? ['tipo' => 'telefono', 'valor' => $telefono]
        : ($bsuid !== null && $bsuid !== '' ? ['tipo' => 'bsuid', 'valor' => $bsuid] : null);

    // Si es mensaje de texto
    if (isset($mensaje['text']['body'])) {
        return [
            'tipo'         => 'texto',
            'telefono'     => $telefono,
            'bsuid'        => $bsuid,
            'destino'      => $destino,
            'mensaje_id'   => $mensaje['id'] ?? null,
            'texto'        => $mensaje['text']['body'],
            'fecha'        => $mensaje['timestamp'] ?? null,
        ];
    }

    // Si es botón interactivo
    if (
        isset($mensaje['interactive']) &&
        $mensaje['interactive']['type'] === 'button_reply'
    ) {
        return [
            'tipo'             => 'boton',
            'telefono'         => $telefono,
            'bsuid'            => $bsuid,
            'destino'          => $destino,
            'mensaje_id'       => $mensaje['id'] ?? null,
            'context_id'       => $mensaje['context']['id'] ?? null,
            'respuesta_id'     => $mensaje['interactive']['button_reply']['id'] ?? null,
            'respuesta_titulo' => $mensaje['interactive']['button_reply']['title'] ?? null,
        ];
    }

    // Si es un estado del sistema (delivered, read, etc.)
    if (isset($status['status'])) {
        return [
            'tipo'               => 'estado',
            'status'             => $status['status'],
            'mensaje_id'         => $status['id'] ?? null,
            'telefono_destino'   => $status['recipient_id'] ?? null,
            'fecha'              => $status['timestamp'] ?? null,
        ];
    }

    return null;
}

$data_entrada = normalizarEntradaWhatsapp($entrada);

// Mensaje priveniente del registro de usuarios
if (($entrada['object'] ?? '') === 'bienvenida') {
	$bienvenida = $mdl_webhook->plantillaBienvenida($entrada['telefono']);
	$mdl_webhook->enviador($bienvenida);
	exit();
}elseif (($entrada['object'] ?? '') === 'cuenta_eliminada') {
	$cuenta_eliminada = $mdl_webhook->responderPlantilla($entrada['telefono'],'cuenta_eliminada');
	$mdl_webhook->enviador($cuenta_eliminada);
	exit();
}

if (!$data_entrada) {
	$resultadoLog = 'ignorado';
	http_response_code(200);
	echo json_encode(['status' => 'OK']);
	exit();
}

$telefono = $data_entrada['telefono'] ?? null;
$bsuid = $data_entrada['bsuid'] ?? null;
$destino = $data_entrada['destino'] ?? null;

if (!$destino) {
	$resultadoLog = 'ignorado';
    http_response_code(200);
    echo json_encode(['status' => 'OK']);
    exit();
}

$usuario = $mdl_webhook->validarUsuario($telefono, $bsuid);

if (!$usuario) {
    $identificador = $telefono ?: $bsuid;
    if ($identificador !== '') {
        $mdl_webhook->usrNoRegistrados($identificador, $fecha_hora);
        $mdl_webhook->LogWebhook($identificador,'Inserción de usuario no registrado');
    }
    if (!$telefono && $bsuid) {
        $enlaceVerificacion = $mdl_webhook->enlaceVerificacionBsuid($bsuid);
        $mensaje = $enlaceVerificacion
            ? 'Para continuar usando la herramienta, verifica tu cuenta y acepta los documentos actualizados aquí: ' . $enlaceVerificacion
            : 'Necesitamos verificar tu cuenta para continuar. Intenta nuevamente en unos momentos.';
    } else {
        $mensaje = 'Parece que tu número no está registrado. Busca tu institución aquí: https://wsp-multi.dcsing.com/view/instituciones/';
    }
    $respuesta = $mdl_webhook->plantillaTexto($destino, $mensaje);
    $mdl_webhook->enviador($respuesta);
    $mdl_webhook->LogWebhook($identificador,'Mensaje de numero no registrado');
    exit();
}

if (!$data_entrada || $data_entrada['tipo'] != 'texto') {
    if ($destino) {
        $mensaje = 'Lo siento, todavía no puedo responder a este tipo de interacciones. ¡Espero poder hacerlo muy pronto!';
        $respuesta = $mdl_webhook->plantillaTexto($destino, $mensaje);
        $mdl_webhook->enviador($respuesta);
    }
    $mdl_webhook->LogWebhook($telefono,'Formato de mensaje recibido no soporteado');
    return;
}

if ($data_entrada['tipo'] == 'audio') {
    $mensaje = 'Lo siento, todavía no puedo responder a este tipo de interacciones. ¡Espero poder hacerlo muy pronto!';
    $respuesta = $mdl_webhook->plantillaTexto($destino, $mensaje);
    $mdl_webhook->enviador($respuesta);
    $mdl_webhook->LogWebhook($telefono,'Formato de mensaje recibido no soporteado');
    return;
}

//.  CLASIFICACION Y BUSQUEDA
$clasificarMensaje = $mdl_webhook->clasificarMensaje($data_entrada['texto']);

$mdl_webhook->nuevoMensajeConversacion($usuario['id_usuario'],'user',$data_entrada['texto'],$fecha_hora);

switch ($clasificarMensaje['codigo']) {
    case 1:
        $mensaje = '¡Hola! 😊 Escríbeme el título, autor o tema que buscas. Idealmente en una frase corta para encontrar mejores resultados 📚';
        $respuesta = $mdl_webhook->plantillaTexto($destino,$mensaje );
        $mdl_webhook->enviador($respuesta);
        break;
    case 2:
        $mensaje = 'Lamentamos que te vayas 😔. Si deseas retirarte del servicio de WhatsApp, por favor realiza el proceso a través de este enlace: https://wsp-multi.dcsing.com/view/eliminar-cuenta/';
        $respuesta = $mdl_webhook->plantillaTexto($destino,$mensaje );
        $mdl_webhook->enviador($respuesta);
        break;
    case 3:
        $mensaje = '¡Claro! 📄 Puedes consultar nuestros Términos y Condiciones en el siguiente enlace: https://wsp-multi.dcsing.com/view/terminos-y-condiciones/';
        $respuesta = $mdl_webhook->plantillaTexto($destino,$mensaje );
        $mdl_webhook->enviador($respuesta);
        break;
    case 4:
        $json_consultarLibros = $mdl_webhook->consultarLibros($data_entrada['telefono'], $data_entrada['texto'], $usuario['id_cliente']);
        $mdl_webhook->logEnRaw($json_consultarLibros);
        $consultarLibros = json_decode((string) $json_consultarLibros, true);
        if (!is_array($consultarLibros)) {
            $consultarLibros = [];
        }

        $mensajesSistema = [];
        if (count($consultarLibros) > 0) {

            foreach ($consultarLibros as $key => $value) {       
                $id_libro = $value['idlibro'] ?? null;
                if (!$id_libro) {
                    continue;
                }
                $enlace = $usuario['url_acceso'].'?il='.$id_libro;
                $url_portada = 'https://ebooks7-24.com/portadas/'.$id_libro.'.jpg';
                $respuesta = $mdl_webhook->plantillaPortada($destino,$url_portada,$enlace);
                $envio = $mdl_webhook->enviador($respuesta);
                $mdl_webhook->logEnRaw("IMG_SEND id={$id_libro} link={$url_portada} envio=" . json_encode($envio));
            }    
        }else{
            $mensaje = 'Lo siento, parece que no hay contenidos relacionados a este termino. ¡Quieres buscar algo más!';
            $mensajesSistema[] = $mensaje;
            $respuesta = $mdl_webhook->plantillaTexto($destino,$mensaje );
            $mdl_webhook->enviador($respuesta);
        }
        $busquedaGeneral = $mdl_webhook->busquedaGeneral($usuario['url_acceso'], $data_entrada['texto']);
        $respuesta = $mdl_webhook->plantillaTexto($destino,$busquedaGeneral);
        $mdl_webhook->enviador($respuesta);
        $mensajesSistema[] = $busquedaGeneral;
        $mensaje = implode("\n", $mensajesSistema);
        break;

}
$mdl_webhook->nuevoMensajeConversacion($usuario['id_usuario'],'system',$mensaje,$fecha_hora);
    
?>
