<?php
require_once __DIR__ . '/../models/mdl_VerificacionBsuid.php';
require_once __DIR__ . '/../models/mdl_Registro.php';

$modelo = new VerificacionBsuid();
header('Content-Type: application/json; charset=utf-8');

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'GET' && ($_GET['op'] ?? '') === 'listarPaises') {
    $registro = new Registro();
    $resultado = $registro->listarPaises();
    $paises = [];
    while ($fila = $resultado->fetch_assoc()) {
        $paises[] = $fila;
    }
    echo json_encode($paises, JSON_UNESCAPED_UNICODE);
    exit();
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || ($_GET['op'] ?? '') !== 'verificar') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'mensaje' => 'Método no permitido']);
    exit();
}

$token = trim((string) ($_POST['token'] ?? ''));
$indicativo = preg_replace('/\D+/', '', (string) ($_POST['indicativo'] ?? ''));
$telefonoLocal = preg_replace('/\D+/', '', (string) ($_POST['telefono'] ?? ''));
$telefono = $indicativo . $telefonoLocal;
$politica = ($_POST['acepta_pol_priv'] ?? '') === '1';
$tyc = ($_POST['acepta_tyc'] ?? '') === '1';

if ($token === '' || !preg_match('/^\d{1,4}$/', $indicativo) || !preg_match('/^\d{6,13}$/', $telefonoLocal) || !preg_match('/^\d{8,15}$/', $telefono) || !$politica || !$tyc) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'mensaje' => 'Confirma un número válido y acepta ambos documentos para continuar.']);
    exit();
}

$dispositivo = substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? 'No informado'), 0, 500);
$ip = filter_var($_SERVER['REMOTE_ADDR'] ?? '', FILTER_VALIDATE_IP) ?: '';
$respuesta = $modelo->verificarYAsociar($token, $telefono, $dispositivo, $ip);

if (($respuesta['codigo'] ?? '') === 'no_registrado') {
    $respuesta['url_registro'] = 'https://wsp-multi.dcsing.com/view/instituciones/';
}
echo json_encode($respuesta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
